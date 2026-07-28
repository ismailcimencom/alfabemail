<?php

namespace App\Filament\Widgets;

use App\Models\Ogrenci;
use App\Models\User;
use App\Services\MailcowService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    protected function getStats(): array
    {
        $mailcow = app(MailcowService::class);
        $mailcowStatus = $mailcow->testConnection();
        
        $ogretmenSayisi = \DB::table('model_has_roles')
            ->where('role_id', \DB::table('roles')->where('name', 'ogretmen')->value('id'))
            ->count();

        return [
            Stat::make('Toplam Öğretmen', $ogretmenSayisi)
                ->description('Sistemdeki öğretmen sayısı')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
            Stat::make('Toplam Öğrenci', Ogrenci::count())
                ->description('Aktif mail kullanan öğrenciler')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Mailcow Durumu', $mailcowStatus ? 'Bağlı' : 'Bağlantı Yok')
                ->description($mailcowStatus ? 'API Erişimi Sağlıklı' : 'Lütfen ayarları kontrol edin')
                ->descriptionIcon($mailcowStatus ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($mailcowStatus ? 'success' : 'danger'),
        ];
    }
}
