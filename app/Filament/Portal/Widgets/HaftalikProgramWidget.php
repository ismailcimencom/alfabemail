<?php

namespace App\Filament\Portal\Widgets;

use App\Models\HaftalikProgram;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class HaftalikProgramWidget extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        if (!SchemaFacade::hasTable('haftalik_programlar')) return false;

        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAnyRole(['ogretmen', 'veli', 'yonetici', 'admin']);
    }

    public function getHeading(): string
    {
        return 'Haftalık Ders Programı';
    }

    public function table(Table $table): Table
    {
        if (!SchemaFacade::hasTable('haftalik_programlar')) {
            return $table->query(HaftalikProgram::whereRaw('1 = 0'));
        }

        $user = auth()->user();
        $sinifIds = collect();

        if ($user?->hasRole('ogretmen')) {
            $sinifIds = $user->ogretmen_siniflar->pluck('id')
                ->merge($user->ogretmen_sinifler_pivot->pluck('id'));
        } elseif ($user?->hasRole('veli')) {
            $veli = $user->veli;
            if ($veli) {
                $sinifIds = $veli->ogrenciler->pluck('sinif_id')->unique();
            }
        } elseif ($user?->hasRole('yonetici')) {
            $sinifIds = $user->okul?->siniflar->pluck('id') ?? collect();
        }

        $gunSirasi = ['pazartesi', 'sali', 'carsamba', 'persembe', 'cuma'];

        return $table
            ->query(
                HaftalikProgram::whereIn('sinif_id', $sinifIds->isEmpty() ? [0] : $sinifIds)
                    ->orderByRaw('FIELD(gun, ?)', [implode(',', $gunSirasi)])
                    ->orderBy('baslangic')
            )
            ->columns([
                TextColumn::make('gun')
                    ->label('Gün')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pazartesi' => 'Pazartesi',
                        'sali' => 'Salı',
                        'carsamba' => 'Çarşamba',
                        'persembe' => 'Perşembe',
                        'cuma' => 'Cuma',
                        default => $state,
                    }),
                TextColumn::make('baslangic')
                    ->label('Başlangıç')
                    ->time('H:i'),
                TextColumn::make('bitis')
                    ->label('Bitiş')
                    ->time('H:i'),
                TextColumn::make('ders_adi')
                    ->label('Ders'),
                TextColumn::make('sinif.ad')
                    ->label('Sınıf'),
                TextColumn::make('ogretmen.name')
                    ->label('Öğretmen'),
            ]);
    }
}
