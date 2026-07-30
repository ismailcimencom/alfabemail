<?php

namespace App\Filament\Portal\Resources\Ogrencis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use App\Models\Sinif;

class OgrenciForm
{
    public static function configure(Schema $schema): Schema
    {
        $isEdit = request()->routeIs('*.edit') || request()->routeIs('*.edit.*');
        
        return $schema
            ->columns(12)
            ->components([
                TextInput::make('first_name')
                    ->label('Ad')
                    ->required()
                    ->columnSpan(6),

                TextInput::make('last_name')
                    ->label('Soyad')
                    ->required()
                    ->columnSpan(6),

                TextInput::make('nickname')
                    ->label('Rumuz (Nickname)')
                    ->suffix('@alfabe.co')
                    ->helperText('Boş bırakılırsa ad.soyad kullanılır')
                    ->columnSpan(12),

                Select::make('sinif_id')
                    ->label('Sınıf')
                    ->options(fn () => auth()->user()?->hasRole('admin')
                        ? Sinif::get()->mapWithKeys(fn ($s) => [$s->id => $s->ad . ' (' . $s->id . ')'])
                        : Sinif::where('ogretmen_user_id', auth()->id())
                            ->orWhereHas('ogretmenler', fn($q) => $q->where('users.id', auth()->id()))
                            ->get()->mapWithKeys(fn ($s) => [$s->id => $s->ad . ' (' . $s->id . ')']))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(12),

                TextInput::make('ad_soyad')
                    ->label('Ad Soyad')
                    ->disabled()
                    ->hidden(!$isEdit)
                    ->columnSpan(12),

                TextInput::make('anne_email')
                    ->label('Anne E-posta 📩')
                    ->email()
                    ->nullable()
                    ->columnSpan(6),

                TextInput::make('baba_email')
                    ->label('Baba E-posta 📩')
                    ->email()
                    ->nullable()
                    ->columnSpan(6),

                TextInput::make('veli_email')
                    ->label('Veli E-posta (Genel)')
                    ->email()
                    ->hidden()
                    ->nullable()
                    ->columnSpan(12),

                TextInput::make('user_email')
                    ->label('Öğrenci E-posta')
                    ->disabled()
                    ->hidden(!$isEdit)
                    ->columnSpan(12),

                TextInput::make('yeni_sifre')
                    ->label('Şifre')
                    ->password()
                    ->minLength(8)
                    ->required(fn () => !$isEdit)
                    ->columnSpan(6),

                TextInput::make('yeni_sifre_tekrar')
                    ->label('Şifre Tekrar')
                    ->password()
                    ->same('yeni_sifre')
                    ->required(fn () => !$isEdit)
                    ->columnSpan(6),
            ]);
    }
}