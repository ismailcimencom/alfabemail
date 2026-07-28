<?php

namespace App\Filament\Portal\Widgets;

use Filament\Widgets\Widget;

class OgrenciListeRehberWidget extends Widget
{
    protected string $view = 'filament.portal.widgets.ogrenci-liste-rehber';

    protected static ?int $sort = 0;

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAnyRole(['ogretmen', 'admin']);
    }
}
