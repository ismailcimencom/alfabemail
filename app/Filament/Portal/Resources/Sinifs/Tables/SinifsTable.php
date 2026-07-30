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
                    ->formatStateUsing(fn ($record) => $record->ad . ' (' . $record->id . ')')
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
                TextColumn::make('ogretmenler')
                    ->label('Öğretmenler')
                    ->getStateUsing(fn ($record): string => $record->ogretmenler->map(fn ($o) =>
                        '<div style="padding:2px 0">' . e($o->name) . '<br><span style="font-size:0.85em;color:var(--gray-500)">'
                        . e($o->email) . ($o->phone ? ' · ' . e($o->phone) : '') . '</span></div>'
                    )->implode(''))
                    ->html(),
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
