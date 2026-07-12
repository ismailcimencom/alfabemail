<?php

namespace App\Filament\Portal\Resources\HaftalikProgramlar\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HaftalikProgramlarTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->label('Ders')
                    ->searchable(),
                TextColumn::make('sinif.ad')
                    ->label('Sınıf'),
                TextColumn::make('ogretmen.name')
                    ->label('Öğretmen'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
