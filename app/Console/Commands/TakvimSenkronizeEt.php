<?php

namespace App\Console\Commands;

use App\Models\BekleyenTakvimEtkinligi;
use App\Models\Ogrenci;
use App\Services\TakvimService;
use Illuminate\Console\Command;

class TakvimSenkronizeEt extends Command
{
    protected $signature = 'takvim:senkronize-et {ogrenci-id? : Belirli bir öğrencinin etkinliklerini senkronize et}';
    protected $description = 'Bekleyen takvim etkinliklerini SOGo CalDAV takvimine ekler';

    public function handle(TakvimService $takvimService): int
    {
        $ogrenciId = $this->argument('ogrenci-id');

        $query = BekleyenTakvimEtkinligi::where('eklendi_mi', false)
            ->where('hata_mi', false);

        if ($ogrenciId) {
            $query->where('ogrenci_id', $ogrenciId);
        }

        $bekleyenler = $query->get();

        if ($bekleyenler->isEmpty()) {
            $this->info('Senkronize edilecek bekleyen etkinlik bulunamadı.');
            return Command::SUCCESS;
        }

        $ogrenciGruplari = $bekleyenler->groupBy('ogrenci_id');

        $toplam = 0;
        $basarili = 0;

        foreach ($ogrenciGruplari as $grupOgrenciId => $etkinlikler) {
            $ogrenci = Ogrenci::find($grupOgrenciId);

            if (!$ogrenci) {
                $this->warn("Öğrenci #{$grupOgrenciId} bulunamadı, atlanıyor.");
                continue;
            }

            $sifre = $this->ogrenciSifresiBul($ogrenci);

            if (!$sifre) {
                $ad = $ogrenci->user?->name ?? "Öğrenci #{$grupOgrenciId}";
                $this->warn("Öğrenci #{$grupOgrenciId} ({$ad}) şifresi bulunamadı, atlanıyor.");
                $etkinlikler->each->update([
                    'hata_mi' => true,
                    'hata_mesaji' => 'Şifre bulunamadı',
                ]);
                continue;
            }

            $ad = $ogrenci->user?->name ?? "Öğrenci #{$grupOgrenciId}";
            $eposta = $ogrenci->user?->email ?? $ogrenci->eposta ?? '';
            $this->info("Senkronize ediliyor: {$ad} ({$eposta})");

            $sonuclar = $takvimService->senkronizeEt($ogrenci, $sifre);

            foreach ($sonuclar as $sonuc) {
                $toplam++;
                if ($sonuc['durum'] === 'eklendi') {
                    $basarili++;
                }
            }

            $this->line("  -> " . count($sonuclar) . " etkinlik işlendi.");
        }

        $this->newLine();
        $this->info("İşlem tamamlandı: {$basarili}/{$toplam} etkinlik başarıyla eklendi.");

        return Command::SUCCESS;
    }

    private function ogrenciSifresiBul(Ogrenci $ogrenci): ?string
    {
        if ($ogrenci->qr_token) {
            $qrData = json_decode($ogrenci->qr_token, true);
            if ($qrData && isset($qrData['password'])) {
                return $qrData['password'];
            }
        }

        if ($ogrenci->user) {
            return null;
        }

        return null;
    }
}
