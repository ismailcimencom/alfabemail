<?php

namespace App\Filament\Portal\Widgets;

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

        if ($user->hasRole('ogretmen')) {
            $sinifIds = Sinif::where('ogretmen_user_id', $user->id)
                ->orWhereHas('ogretmenler', fn($q) => $q->where('users.id', $user->id))
                ->pluck('id');

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
