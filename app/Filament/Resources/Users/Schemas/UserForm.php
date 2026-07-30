<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Ogrenci;
use App\Models\Sinif;
use App\Services\StudentCreationService;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        $isCreate = fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord;
        $ogrenciRoleId = Role::where('name', 'ogrenci')->value('id');
        $isOgrenci = fn (callable $get) => $ogrenciRoleId && $get('roles') == $ogrenciRoleId;
        $ogretmenRoleId = Role::where('name', 'ogretmen')->value('id');
        $isOgretmen = fn (callable $get) => $ogretmenRoleId && $get('roles') == $ogretmenRoleId;
        $veliRoleId = Role::where('name', 'veli')->value('id');
        $isVeli = fn (callable $get) => $veliRoleId && $get('roles') == $veliRoleId;

        return $schema
            ->columns(2)
            ->components([

                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->required()
                    ->maxLength(255)
                    ->hidden(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get))
                    ->columnSpanFull(),

                TextInput::make('first_name')
                    ->label('Ad')
                    ->required(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get))
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get))
                    ->maxLength(255)
                    ->columnSpan(1),

                TextInput::make('last_name')
                    ->label('Soyad')
                    ->required(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get))
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get))
                    ->maxLength(255)
                    ->columnSpan(1),

                TextInput::make('nickname')
                    ->label('Rumuz (Nickname)')
                    ->suffix('@alfabe.co')
                    ->helperText('Boş bırakılırsa ad.soyad kullanılır')
                    ->hidden(fn (callable $get, $livewire) => !$isCreate($livewire) || !$isOgrenci($get))
                    ->columnSpanFull(),

                Select::make('sinif_id')
                    ->label('Sınıf')
                    ->options(fn () => Sinif::get()->mapWithKeys(fn ($s) => [$s->id => $s->ad . ' (' . $s->id . ')']))
                    ->searchable()
                    ->preload()
                    ->hidden(fn (callable $get) => !$isOgrenci($get))
                    ->columnSpanFull(),

                TextInput::make('anne_email')
                    ->label('Anne E-posta')
                    ->email()
                    ->nullable()
                    ->hidden(fn (callable $get) => !$isOgrenci($get))
                    ->columnSpan(1),

                TextInput::make('baba_email')
                    ->label('Baba E-posta')
                    ->email()
                    ->nullable()
                    ->hidden(fn (callable $get) => !$isOgrenci($get))
                    ->columnSpan(1),

                TextInput::make('anne_telefon')
                    ->label('Anne Telefon')
                    ->tel()
                    ->nullable()
                    ->maxLength(20)
                    ->hidden(fn (callable $get) => !$isOgrenci($get))
                    ->columnSpan(1),

                TextInput::make('baba_telefon')
                    ->label('Baba Telefon')
                    ->tel()
                    ->nullable()
                    ->maxLength(20)
                    ->hidden(fn (callable $get) => !$isOgrenci($get))
                    ->columnSpan(1),

                TextInput::make('email')
                    ->label('Email address')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($livewire) => !$isCreate($livewire))
                    ->hidden(fn (callable $get, $livewire) => $isCreate($livewire) && $isOgrenci($get))
                    ->dehydrated(fn ($livewire) => $isCreate($livewire))
                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 5) . '*****' : null)
                    ->columnSpanFull(),

                Select::make('roles')
                    ->options(fn () => Role::where('name', '!=', 'yonetici')->pluck('name', 'id'))
                    ->preload()
                    ->searchable()
                    ->live()
                    ->columnSpanFull(),

                TextInput::make('phone')
                    ->label(fn (callable $get) => match (true) {
                        $isOgretmen($get) => 'Öğretmen Telefon',
                        $isVeli($get) => match ($get('veli_type')) {
                            'anne' => 'Anne Telefon',
                            'baba' => 'Baba Telefon',
                            'diger' => 'Veli Telefon',
                            default => 'Veli Telefon',
                        },
                        default => 'Telefon',
                    })
                    ->tel()
                    ->maxLength(20)
                    ->hidden(fn (callable $get) => $isOgrenci($get))
                    ->columnSpanFull(),

                Select::make('sinif_ids')
                    ->label('Sınıflar')
                    ->multiple()
                    ->options(fn () => Sinif::get()->mapWithKeys(fn ($s) => [$s->id => $s->ad . ' (' . $s->id . ')']))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('ad')->label('Sınıf Adı')->required()->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Sinif::create(['ad' => $data['ad']])->id;
                    })
                    ->createOptionModalHeading('Yeni Sınıf Oluştur')
                    ->hidden(fn (callable $get) => !$isOgretmen($get))
                    ->columnSpanFull(),

                Select::make('ogrenci_ids')
                    ->label('Öğrenciler')
                    ->multiple()
                    ->options(fn () => Ogrenci::join('users', 'ogrenciler.user_id', '=', 'users.id')
                        ->pluck('users.name', 'ogrenciler.id'))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('first_name')->label('Ad')->required()->maxLength(255),
                        TextInput::make('last_name')->label('Soyad')->required()->maxLength(255),
                        TextInput::make('nickname')->label('Rumuz')->suffix('@alfabe.co')
                            ->helperText('Boş bırakılırsa ad.soyad kullanılır'),
                        Select::make('sinif_id')->label('Sınıf')
                            ->options(fn () => Sinif::get()->mapWithKeys(fn ($s) => [$s->id => $s->ad . ' (' . $s->id . ')']))
                            ->searchable(),
                        TextInput::make('password')->label('Şifre')->password()->required()
                            ->default('Ogrenci123!'),
                        TextInput::make('anne_email')->label('Anne E-posta')->email()->nullable(),
                        TextInput::make('anne_telefon')->label('Anne Telefon')->tel()->nullable()->maxLength(20),
                        TextInput::make('baba_email')->label('Baba E-posta')->email()->nullable(),
                        TextInput::make('baba_telefon')->label('Baba Telefon')->tel()->nullable()->maxLength(20),
                    ])
                    ->createOptionUsing(function (array $data) {
                        try {
                            $ogrenci = app(StudentCreationService::class)->create($data);

                            Notification::make()->title('Başarılı')
                                ->body("Öğrenci {$ogrenci->user->name} oluşturuldu.")
                                ->success()->send();

                            return $ogrenci->id;
                        } catch (\RuntimeException $e) {
                            Notification::make()->title('Hata')->body($e->getMessage())->danger()->send();
                            return null;
                        }
                    })
                    ->createOptionModalHeading('Yeni Öğrenci Oluştur')
                    ->hidden(fn (callable $get) => !$isVeli($get))
                    ->columnSpanFull(),

                Select::make('veli_type')
                    ->label('Veli Türü')
                    ->options([
                        'anne' => 'Anne',
                        'baba' => 'Baba',
                        'diger' => 'Diğer',
                    ])
                    ->nullable()
                    ->hidden(fn (callable $get) => !$isVeli($get))
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->columnSpanFull(),

                TextInput::make('password')
                    ->label('Yeni Şifre')
                    ->password()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->columnSpan(1),

                TextInput::make('password_confirmation')
                    ->label('Yeni Şifre Tekrar')
                    ->password()
                    ->dehydrated(false)
                    ->same('password')
                    ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                    ->columnSpan(1),
            ]);
    }
}
