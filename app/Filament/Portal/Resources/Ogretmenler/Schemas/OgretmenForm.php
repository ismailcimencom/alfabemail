<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Schemas;

use App\Models\Sinif;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;

class OgretmenForm
{
    public static function configure(Schema $schema): Schema
    {
        $user = auth()->user();
        $isAdmin = $user?->hasRole('admin') ?? false;
        $userOkulId = $user?->okul?->id ?? $user?->bagli_okul?->id;
        $isEdit = request()->route('record') !== null;

        return $schema
            ->components([
                TextInput::make('email')
                    ->label('E-posta')
                    ->email()
                    ->required()
                    ->unique('users', 'email', ignoreRecord: true)
                    ->placeholder('ornek@okul.com'),

                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->maxLength(255)
                    ->visible($isEdit),

                TextInput::make('phone')
                    ->label('Telefon')
                    ->tel()
                    ->maxLength(20)
                    ->visible($isEdit),

                Select::make('sinif_ids')
                    ->label('Sınıflar')
                    ->multiple()
                    ->options(fn () => $userOkulId ? Sinif::where('okul_id', $userOkulId)->pluck('ad', 'id') : [])
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('ad')->label('Sınıf Adı')->required()->maxLength(255),
                        \Filament\Forms\Components\Hidden::make('okul_id')
                            ->default($userOkulId),
                    ])
                    ->createOptionUsing(function (array $data) {
                        if (!$data['okul_id']) {
                            Notification::make()->title('Okul bulunamadı')->warning()->send();
                            return null;
                        }
                        return Sinif::create(['ad' => $data['ad'], 'okul_id' => $data['okul_id']])->id;
                    })
                    ->createOptionModalHeading('Yeni Sınıf Oluştur'),

                TextInput::make('password')
                    ->label('Yeni Şifre')
                    ->password()
                    ->minLength(6)
                    ->visible($isEdit),

                TextInput::make('password_confirmation')
                    ->label('Şifre Tekrar')
                    ->password()
                    ->same('password')
                    ->visible($isEdit),
            ]);
    }
}
