<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendingUserResource\Pages;
use App\Models\PendingUser;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PendingUserResource extends Resource
{
    protected static ?string $model = PendingUser::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Yeni Kullanıcılar';

    protected static ?string $pluralModelLabel = 'Yeni Kullanıcılar';

    protected static ?string $modelLabel = 'Yeni Kullanıcı';

    protected static ?string $slug = 'yeni-kullanicilar';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Ad Soyad')
                    ->disabled(),
                TextInput::make('email')
                    ->label('E-posta')
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Telefon')
                    ->disabled(),
                TextInput::make('school')
                    ->label('Okul')
                    ->disabled(),
                DateTimePicker::make('email_verified_at')
                    ->label('E-posta Doğrulama')
                    ->disabled(),
                TextInput::make('status')
                    ->label('Durum')
                    ->disabled(),
                DateTimePicker::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->disabled(),
                Select::make('assigned_role')
                    ->label('Atanacak Rol')
                    ->options([
                        'yonetici' => 'Yönetici',
                        'ogretmen' => 'Öğretmen',
                        'veli' => 'Veli',
                    ])
                    ->visible(fn ($record) => $record?->status === 'pending'),
                Textarea::make('rejection_reason')
                    ->label('Reddetme Sebebi')
                    ->visible(fn ($record) => $record?->status === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('school')
                    ->label('Okul')
                    ->searchable()
                    ->placeholder('—'),
                IconColumn::make('email_verified_at')
                    ->label('Doğrulandı')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('assigned_role')
                    ->label('Rol')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'yonetici' => 'Yönetici',
                        'ogretmen' => 'Öğretmen',
                        'veli' => 'Veli',
                        default => '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'yonetici' => 'success',
                        'ogretmen' => 'primary',
                        'veli' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Bekliyor',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                    }),
                TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'pending' => 'Bekliyor',
                        'rejected' => 'Reddedildi',
                    ])
                    ->default('pending'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        TextInput::make('name')
                            ->label('Ad Soyad')
                            ->default(fn ($record) => $record->name)
                            ->required(),
                        TextInput::make('school')
                            ->label('Okul Adı')
                            ->default(fn ($record) => $record->school)
                            ->required(),
                        Select::make('assigned_role')
                            ->label('Rol')
                            ->options([
                                'yonetici' => 'Yönetici',
                                'ogretmen' => 'Öğretmen',
                                'veli' => 'Veli',
                            ])
                            ->default(fn ($record) => $record->assigned_role)
                            ->required(),
                    ])
                    ->action(function (PendingUser $record, array $data) {
                        $existingUser = User::where('email', $record->email)->first();
                        if ($existingUser) {
                            Notification::make()
                                ->title('Bu e-posta adresi zaten kayıtlı.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $okul = \App\Models\Okul::where('ad', $data['school'])->first();
                        if (!$okul) {
                            $okul = \App\Models\Okul::create([
                                'ad' => $data['school'],
                                'is_active' => true,
                                'durum' => 'onayli',
                            ]);
                        }

                        $user = User::create([
                            'name' => $data['name'],
                            'email' => $record->email,
                            'password' => $record->password ?? \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                            'phone' => $record->phone,
                            'okul_id' => $okul->id,
                            'is_active' => true,
                        ]);
                        $user->assignRole($data['assigned_role']);

                        if ($data['assigned_role'] === 'yonetici' && $okul) {
                            $okul->update(['yonetici_user_id' => $user->id]);
                        }

                        if ($data['assigned_role'] === 'veli') {
                            \App\Models\Veli::create(['user_id' => $user->id]);
                        }

                        $record->delete();
                        Notification::make()->title('Kullanıcı onaylandı ve kullanıcılara taşındı.')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reddetme Sebebi (isteğe bağlı)'),
                    ])
                    ->action(function (PendingUser $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'] ?? null,
                        ]);
                        Notification::make()->title('Kullanıcı reddedildi.')->danger()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendingUsers::route('/'),
            'edit' => Pages\EditPendingUser::route('/{record}/edit'),
        ];
    }
}
