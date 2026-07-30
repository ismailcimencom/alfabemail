<?php

namespace App\Filament\Portal\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\MailAktiviteLog;
use Illuminate\Support\Facades\Auth;

class OgrenciAktiviteWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Toplam Mail Grafiği';

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('veli') ?? false;
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $veli = $user->veli;

        if (!$veli) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $ogrenciIds = $veli->ogrenciler()->pluck('id');

        $labels = [];
        $gonderilenData = [];
        $alinanData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();

            $gonderilenData[] = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
                ->where('tip', 'gonderilen')
                ->whereBetween('tarih', [$start, $end])
                ->count();

            $alinanData[] = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
                ->where('tip', 'alinan')
                ->whereBetween('tarih', [$start, $end])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Gönderilen',
                    'data' => $gonderilenData,
                    'borderColor' => '#7fa7ff',
                    'backgroundColor' => 'rgba(127, 167, 255, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Alınan',
                    'data' => $alinanData,
                    'borderColor' => '#c4ffe7',
                    'backgroundColor' => 'rgba(196, 255, 231, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
