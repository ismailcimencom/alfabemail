<?php

namespace App\Filament\Portal\Widgets;

use App\Models\MailAktiviteLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class VeliDashboardWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        $veli = $user->veli;

        if (!$veli) {
            return $this->emptyStats();
        }

        $ogrenciIds = $veli->ogrenciler()->pluck('id');

        $totalMails = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)->count();

        return [
            Stat::make('Toplam Mail', $totalMails)
                ->description('Toplam gönderilen/alınan')
                ->icon('heroicon-o-envelope')
                ->color('primary'),
            Stat::make('Kayıtlı Öğrenci', $veli->ogrenciler()->count())
                ->description('Takip ettiğiniz öğrenci sayısı')
                ->icon('heroicon-o-academic-cap')
                ->color('warning'),
        ];
    }

    private function emptyStats(): array
    {
        return [
            Stat::make('Toplam Mail', 0)
                ->description('Toplam gönderilen/alınan')
                ->icon('heroicon-o-envelope')
                ->color('primary'),
            Stat::make('Kayıtlı Öğrenci', 0)
                ->description('Takip ettiğiniz öğrenci sayısı')
                ->icon('heroicon-o-academic-cap')
                ->color('warning'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('veli') ?? false;
    }
}
