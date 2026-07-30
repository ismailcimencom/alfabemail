<?php

namespace App\Filament\Portal\Resources\Sinifs\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\User;

class SinifForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = auth()->user();
        $isAdmin = $user?->hasRole('admin') ?? false;

        return $schema
            ->columns(12)
            ->components([
                TextInput::make('id')
                    ->label('Sınıf ID')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpan(12)
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

                TextInput::make('ad')
                    ->label('Sınıf Adı')
                    ->placeholder('Örn: 5-A')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(12)
                    ->disabled(!$isAdmin),

                TextInput::make('ad')
                    ->label('Sınıf Adı')
                    ->placeholder('Örn: 5-A')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(12)
                    ->visible(!$isAdmin),

                Select::make('durum')
                    ->label('Durum')
                    ->options([
                        'aktif' => 'Aktif',
                        'beklemede' => 'Beklemede',
                    ])
                    ->default('aktif')
                    ->visible($isAdmin)
                    ->columnSpan(12),

                Repeater::make('sinif_ogretmenleri')
                    ->label('Sınıftaki Öğretmenler')
                    ->schema([
                        TextInput::make('name')
                            ->label('Ad Soyad')
                            ->disabled(),
                        TextInput::make('email')
                            ->label('E-posta')
                            ->disabled(),
                        TextInput::make('phone')
                            ->label('Telefon')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->default(function () {
                        $sinifId = request()->route('record');
                        if ($sinifId) {
                            $sinif = \App\Models\Sinif::with('ogretmenler')->find($sinifId);
                            if ($sinif && $sinif->ogretmenler->isNotEmpty()) {
                                return $sinif->ogretmenler->map(fn ($o) => [
                                    'name' => $o->name,
                                    'email' => $o->email,
                                    'phone' => $o->phone ?? '—',
                                ])->toArray();
                            }
                        }
                        $user = auth()->user();
                        if ($user) {
                            return [[
                                'name' => $user->name,
                                'email' => $user->email,
                                'phone' => $user->phone ?? '—',
                            ]];
                        }
                        return [];
                    })
                    ->columnSpan(12),
            ]);
    }
}
