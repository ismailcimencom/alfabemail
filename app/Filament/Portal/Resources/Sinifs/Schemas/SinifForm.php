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
        $isYonetici = $user?->hasAnyRole(['admin', 'yonetici']) ?? false;
        $isOgretmen = $user?->hasRole('ogretmen') ?? false;

        return $schema
            ->columns(12)
            ->components([
                TextInput::make('ad')
                    ->label('Sınıf Adı')
                    ->placeholder('Örn: 5-A')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(12)
                    ->disabled(!$isYonetici),

                TextInput::make('ad')
                    ->label('Sınıf Adı')
                    ->placeholder('Örn: 5-A')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(12)
                    ->visible($isOgretmen),

                Select::make('okul_id')
                    ->label('Okul')
                    ->relationship('okul', 'ad')
                    ->required()
                    ->default(fn () => auth()->user()?->okul?->id ?? auth()->user()?->bagli_okul?->id)
                    ->columnSpan(12)
                    ->disabled(!$isYonetici)
                    ->visible($isYonetici),

                Select::make('ogretmenler')
                    ->label('Öğretmenler')
                    ->options(function () {
                        return User::whereHas('roles', fn ($q) => $q->where('name', 'ogretmen'))
                            ->pluck('name', 'id');
                    })
                    ->multiple()
                    ->searchable()
                    ->visible($isYonetici)
                    ->columnSpan(12),

                Select::make('durum')
                    ->label('Durum')
                    ->options([
                        'aktif' => 'Aktif',
                        'beklemede' => 'Beklemede',
                    ])
                    ->default('aktif')
                    ->visible($isYonetici)
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
                        if (!$sinifId) return [];
                        $sinif = \App\Models\Sinif::with('ogretmenler')->find($sinifId);
                        if (!$sinif) return [];
                        return $sinif->ogretmenler->map(fn ($o) => [
                            'name' => $o->name,
                            'email' => $o->email,
                            'phone' => $o->phone ?? '—',
                        ])->toArray();
                    })
                    ->columnSpan(12),
            ]);
    }
}
