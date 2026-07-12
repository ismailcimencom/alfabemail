<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Okul;
use App\Models\Sinif;
use App\Models\Ogrenci;
use App\Models\MailAktiviteLog;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortalStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $user = auth()->user();
        
        if (!$user) {
            return [];
        }

        $stats = [];

        if ($user->hasRole('yonetici')) {
            $okulId = $user->okul?->id;
            
            if (!$okulId) {
                $stats[] = Stat::make('Uyarı', 'Okul bulunamadı')
                    ->description('Bu hesaba bağlı okul yok')
                    ->color('danger');
            } else {
                $sinifIds = Sinif::where('okul_id', $okulId)->pluck('id');
                $ogrenciIds = Ogrenci::whereIn('sinif_id', $sinifIds)->pluck('id');

                $ogretmenSayisi = \DB::table('ogretmen_sinif')
                    ->whereIn('sinif_id', $sinifIds)
                    ->distinct('ogretmen_user_id')
                    ->count();

                $stats[] = Stat::make('Öğretmenler', $ogretmenSayisi)
                    ->description('Okulunuzdaki aktif öğretmenler')
                    ->color('primary');

                $stats[] = Stat::make('Sınıflar', $sinifIds->count())
                    ->color('info');

                $stats[] = Stat::make('Öğrenciler', $ogrenciIds->count())
                    ->color('success');

                $gonderilen = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
                    ->where('tip', 'gonderilen')->count();

                $alinan = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
                    ->where('tip', 'alinan')->count();

                $stats[] = Stat::make('Gönderilen Mail', $gonderilen)
                    ->description('Öğrencilerden gönderilen toplam mail')
                    ->color('warning');

                $stats[] = Stat::make('Alınan Mail', $alinan)
                    ->description('Öğrencilere gelen toplam mail')
                    ->color('gray');
            }
        }

        if ($user->hasRole('ogretmen')) {
            $sinifIds = DB::table('ogretmen_sinif')
                ->where('ogretmen_user_id', $user->id)
                ->pluck('sinif_id');
            
            $ogrenciIds = Ogrenci::whereIn('sinif_id', $sinifIds)->pluck('id');

            $stats[] = Stat::make('Sınıflarım', $sinifIds->count())
                ->color('primary');

            $stats[] = Stat::make('Öğrencilerim', $ogrenciIds->count())
                ->description('Mailbox sahibi öğrencileriniz')
                ->color('success');

            $gonderilen = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
                ->where('tip', 'gonderilen')->count();

            $alinan = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
                ->where('tip', 'alinan')->count();

            $stats[] = Stat::make('Gönderilen Mail', $gonderilen)
                ->description('Öğrencilerden gönderilen')
                ->color('warning');

            $stats[] = Stat::make('Alınan Mail', $alinan)
                ->description('Öğrencilere gelen')
                ->color('gray');
        }

        return $stats;
    }
}
