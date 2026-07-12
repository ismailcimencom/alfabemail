<?php

namespace App\Services;

use App\Models\Ogrenci;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TakvimService
{
    private string $caldavBaseUrl = 'https://mail.alfabe.co/SOGo/dav';

    public function senkronizeEt(Ogrenci $ogrenci, string $sifre): array
    {
        $sonuclar = [];
        $bekleyenler = $ogrenci->bekleyenTakvimEtkinlikleri()
            ->where('eklendi_mi', false)
            ->where('hata_mi', false)
            ->get();

        foreach ($bekleyenler as $etkinlik) {
            $sonuc = $this->eventEkle(
                eposta: $ogrenci->eposta,
                sifre: $sifre,
                baslik: $etkinlik->baslik,
                aciklama: $etkinlik->aciklama ?? '',
                teslimTarihi: $etkinlik->teslim_tarihi,
                etkinlikId: $etkinlik->odev_id,
            );

            if ($sonuc) {
                $etkinlik->update(['eklendi_mi' => true]);
                $sonuclar[] = [
                    'etkinlik_id' => $etkinlik->id,
                    'odev_id' => $etkinlik->odev_id,
                    'durum' => 'eklendi',
                ];
            } else {
                $etkinlik->update([
                    'hata_mi' => true,
                    'hata_mesaji' => 'CalDAV sunucusu yanıt vermedi',
                ]);
                $sonuclar[] = [
                    'etkinlik_id' => $etkinlik->id,
                    'odev_id' => $etkinlik->odev_id,
                    'durum' => 'hata',
                ];
            }
        }

        return $sonuclar;
    }

    public function eventEkle(
        string $eposta,
        string $sifre,
        string $baslik,
        string $aciklama,
        string $teslimTarihi,
        int $etkinlikId,
    ): bool {
        $uuid = 'alfabe-odev-' . $etkinlikId . '-' . Str::uuid();
        $ics = $this->icsOlustur($uuid, $baslik, $aciklama, $teslimTarihi);
        $url = rtrim($this->caldavBaseUrl, '/')
            . '/' . rawurlencode($eposta)
            . '/Calendar/personal/'
            . $uuid . '.ics';

        try {
            $response = Http::withBasicAuth($eposta, $sifre)
                ->withBody($ics, 'text/calendar; charset=utf-8')
                ->withOptions(['verify' => false])
                ->put($url);

            return $response->successful();
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    public function eventSil(string $eposta, string $sifre, int $odevId): bool
    {
        $url = rtrim($this->caldavBaseUrl, '/')
            . '/' . rawurlencode($eposta)
            . '/Calendar/personal/alfabe-odev-' . $odevId . '.ics';

        try {
            $response = Http::withBasicAuth($eposta, $sifre)
                ->withOptions(['verify' => false])
                ->delete($url);

            return $response->successful();
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }

    private function icsOlustur(string $uuid, string $baslik, string $aciklama, string $teslimTarihi): string
    {
        $dtStart = date('Ymd\T235900\Z', strtotime($teslimTarihi));
        $dtEnd = date('Ymd\T235900\Z', strtotime($teslimTarihi));
        $now = date('Ymd\THis\Z');

        return implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ALFABE//TR',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' . $uuid,
            'DTSTAMP:' . $now,
            'DTSTART:' . $dtStart,
            'DTEND:' . $dtEnd,
            'SUMMARY:' . $this->icsEncode($baslik),
            'DESCRIPTION:' . $this->icsEncode($aciklama),
            'BEGIN:VALARM',
            'TRIGGER:-P1D',
            'ACTION:DISPLAY',
            'DESCRIPTION:' . $this->icsEncode('Hatırlatma: ' . $baslik),
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);
    }

    private function icsEncode(string $text): string
    {
        $text = preg_replace('/\r\n|\r|\n/', '\\n', $text);
        return str_replace([',', ';', '\\'], ['\\,', '\\;', '\\\\'], $text);
    }
}
