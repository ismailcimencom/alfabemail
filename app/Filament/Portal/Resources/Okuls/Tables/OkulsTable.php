<?php

namespace App\Filament\Portal\Resources\Okuls\Tables;

use App\Models\Okul;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OkulsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ad')
                    ->label('Okul Adı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sehir')
                    ->label('İl')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ilce')
                    ->label('İlçe')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('yonetici.name')
                    ->label('Yönetici')
                    ->searchable(),
                TextColumn::make('telefon')
                    ->label('Telefon'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('durum')
                    ->label('Onay')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'onayli' => 'success',
                        'reddet' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'onayli' => '✅ Onaylı',
                        'reddet' => '❌ Reddedildi',
                        default => '⏳ Beklemede',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('sehir')
                    ->label('İl')
                    ->options(fn () => \App\Models\Okul::pluck('sehir', 'sehir')->filter()->toArray()),
                TernaryFilter::make('is_active')
                    ->label('Durum')
                    ->placeholder('Tümü')
                    ->trueLabel('Aktif')
                    ->falseLabel('Pasif'),
                SelectFilter::make('durum')
                    ->label('Onay Durumu')
                    ->options([
                        'beklemede' => '⏳ Beklemede',
                        'onayli' => '✅ Onaylı',
                        'reddet' => '❌ Reddedildi',
                    ])
                    ->visible(fn () => auth()->user()?->hasRole('admin') ?? false),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->hasRole('admin') ?? false),
                Action::make('onayla')
                    ->label(fn (Okul $record) => $record->durum === 'onayli' ? 'Onayı Kaldır' : 'Onayla')
                    ->icon(fn (Okul $record) => $record->durum === 'onayli' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Okul $record) => $record->durum === 'onayli' ? 'danger' : 'success')
                    ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                    ->requiresConfirmation()
                    ->action(function (Okul $record) {
                        $yeniDurum = $record->durum === 'onayli' ? 'beklemede' : 'onayli';
                        $record->update(['durum' => $yeniDurum]);
                        Notification::make()
                            ->title($yeniDurum === 'onayli' ? 'Onaylandı' : 'Onay Kaldırıldı')
                            ->body($record->ad . ' ' . ($yeniDurum === 'onayli' ? 'onaylandı' : 'onayı kaldırıldı'))
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('admin') ?? false),
                ]),
            ]);
    }
}
