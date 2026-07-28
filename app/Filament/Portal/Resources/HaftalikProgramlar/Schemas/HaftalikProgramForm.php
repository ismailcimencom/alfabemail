<?php

namespace App\Filament\Portal\Resources\HaftalikProgramlar\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use App\Models\User;

class HaftalikProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Select::make('gun')
                    ->label('Gün')
                    ->options([
                        'pazartesi' => 'Pazartesi',
                        'sali' => 'Salı',
                        'carsamba' => 'Çarşamba',
                        'persembe' => 'Perşembe',
                        'cuma' => 'Cuma',
                    ])
                    ->required()
                    ->columnSpan(4),

                TimePicker::make('baslangic')
                    ->label('Başlangıç')
                    ->required()
                    ->columnSpan(4),

                TimePicker::make('bitis')
                    ->label('Bitiş')
                    ->required()
                    ->columnSpan(4),

                TextInput::make('ders_adi')
                    ->label('Ders Adı')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(12),

                Select::make('sinif_id')
                    ->label('Sınıf')
                    ->options(function () {
                        return \App\Models\Sinif::pluck('ad', 'id');
                    })
                    ->required()
                    ->columnSpan(6),

                Select::make('ogretmen_id')
                    ->label('Öğretmen')
                    ->options(function () {
                        return User::whereHas('roles', fn ($q) => $q->where('name', 'ogretmen'))
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->nullable()
                    ->columnSpan(6),
            ]);
    }
}
