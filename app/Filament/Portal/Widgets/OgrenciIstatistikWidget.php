<?php

namespace App\Filament\Portal\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Ogrenci;
use App\Models\MailAktiviteLog;
use Illuminate\Support\Facades\Auth;

class OgrenciIstatistikWidget extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Aylık Mail İstatistikleri';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false;
    }

    protected function getData(): array
    {
        $user = Auth::user();

        $ogrenciIds = $user->hasRole('ogretmen')
            ? Ogrenci::whereHas('sinif', fn($q) => $q
                ->where('ogretmen_user_id', $user->id)
                ->orWhereHas('ogretmenler', fn($q2) => $q2->where('users.id', $user->id))
            )->pluck('id')
            : Ogrenci::pluck('id');

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
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Alınan',
                    'data' => $alinanData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
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