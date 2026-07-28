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

    protected ?string $heading = 'Tüm Zamanlar Mail İstatistikleri';

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

        $gonderilen = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)->where('tip', 'gonderilen')->count();
        $alinan = MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)->where('tip', 'alinan')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Gönderilen',
                    'data' => [$gonderilen],
                    'backgroundColor' => '#7fa7ff',
                ],
                [
                    'label' => 'Alınan',
                    'data' => [$alinan],
                    'backgroundColor' => '#c4ffe7',
                ],
            ],
            'labels' => ['Tüm Zamanlar'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}