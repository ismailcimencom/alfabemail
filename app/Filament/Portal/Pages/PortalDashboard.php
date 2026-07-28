<?php

namespace App\Filament\Portal\Pages;

use Filament\Pages\Dashboard;

class PortalDashboard extends Dashboard
{
    protected static ?string $navigationLabel = 'Portal';

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function getHeading(): string
    {
        $user = auth()->user();

        if (!$user) {
            return 'Portal';
        }

        if ($user->hasRole('ogretmen')) {
            return 'Öğretmen Paneli';
        }

        if ($user->hasRole('veli')) {
            return 'Veli Paneli';
        }

        if ($user->hasRole('ogrenci')) {
            return 'Öğrenci Paneli';
        }

        return 'Portal';
    }

    public function getSubheading(): ?string
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if ($user->hasRole('ogretmen')) {
            return 'Toplu öğrenci mail kaydı ve süreç yönetimi paneline hoş geldiniz.';
        }

        if ($user->hasRole('veli')) {
            return 'Öğrenci mail gelişim takibi ve etkinlik özet raporları paneline hoş geldiniz.';
        }

        return 'Alfabe Mail portalına hoş geldiniz.';
    }
}