<?php

namespace App\Filament\Portal\Resources\Sinifs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SinifsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ad')
                    ->label('Sınıf Adı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('durum')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'beklemede' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'beklemede' => 'Onay Bekliyor',
                        default => $state,
                    }),
                TextColumn::make('ogretmenler.name')
                    ->label('Öğretmenler')
                    ->badge()
                    ->limitList(3),
                TextColumn::make('ogrenciler_count')
                    ->label('Öğrenci Sayısı')
                    ->counts('ogrenciler'),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
