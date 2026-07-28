<?php

namespace App\Filament\Portal\Widgets;

use App\Models\Sinif;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Ogrenci;
use App\Models\MailAktiviteLog;
use Illuminate\Support\Facades\Auth;

class OgrenciIstatistikKartlariWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $user = Auth::user();
        $baseQuery = Ogrenci::query();

        if ($user->hasRole('ogretmen')) {
            $baseQuery->whereHas('sinif', fn($q) => $q
                ->where('ogretmen_user_id', $user->id)
                ->orWhereHas('ogretmenler', fn($q2) => $q2->where('users.id', $user->id))
            );
        }

        $sinifSayisi = $user->hasRole('ogretmen')
            ? Sinif::where('ogretmen_user_id', $user->id)
                ->orWhereHas('ogretmenler', fn($q) => $q->where('users.id', $user->id))
                ->count()
            : 0;

        $toplamOgrenci = (clone $baseQuery)->count();
        $aktifOgrenci = (clone $baseQuery)->whereHas('user', fn($q) => $q->where('is_active', true))->count();
        $pasifOgrenci = $toplamOgrenci - $aktifOgrenci;

        $ogrenciIds = (clone $baseQuery)->pluck('id');
        $gonderilen = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
            ->where('tip', 'gonderilen')
            ->count();
        $alinan = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
            ->where('tip', 'alinan')
            ->count();

        return [
            Stat::make('Toplam Öğrenci', $toplamOgrenci)
                ->description('Sisteme kayıtlı')
                ->icon('heroicon-o-users'),
            Stat::make('Aktif Öğrenci', $aktifOgrenci)
                ->description('Giriş yapabilen')
                ->icon('heroicon-o-check-circle'),
            Stat::make('Pasif Öğrenci', $pasifOgrenci)
                ->description('Giriş yapamayan')
                ->icon('heroicon-o-minus-circle')
                ->color('danger'),
            Stat::make('Sınıflarım', $sinifSayisi)
                ->description('Sorumlu olduğunuz sınıflar')
                ->icon('heroicon-o-rectangle-stack')
                ->color('primary'),
            Stat::make('Gönderilen', $gonderilen)
                ->description('Öğrencilerden gönderilen')
                ->icon('heroicon-o-paper-airplane')
                ->color('success'),
            Stat::make('Alınan', $alinan)
                ->description('Öğrencilere gelen')
                ->icon('heroicon-o-inbox')
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false;
    }
}