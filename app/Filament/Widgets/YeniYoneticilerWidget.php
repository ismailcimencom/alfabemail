<?php

namespace App\Filament\Widgets;

use App\Models\Okul;
use App\Models\User;
use Filament\Widgets\Widget;

class YeniYoneticilerWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.yeni-yoneticiler';
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function getViewData(): array
    {
        $sonOkullar = Okul::where('is_active', true)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($okul) {
                $yonetici = User::with('roles')->find($okul->yonetici_user_id);
                return [
                    'okul' => $okul->ad,
                    'yonetici' => $yonetici?->name ?? '—',
                    'email' => $yonetici?->email ?? '—',
                    'tarih' => $okul->created_at->format('d.m.Y H:i'),
                ];
            });

        return [
            'okullar' => $sonOkullar,
            'toplam' => Okul::where('is_active', true)->count(),
        ];
    }
}
