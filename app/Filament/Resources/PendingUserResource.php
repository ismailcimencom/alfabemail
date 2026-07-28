<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendingUserResource\Pages;
use App\Models\PendingUser;
use App\Models\User;
use App\Mail\ApprovalMail;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

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
                TextInput::make('name')->label('Ad Soyad')->disabled(),
                TextInput::make('email')->label('E-posta')->disabled(),
                TextInput::make('phone')->label('Telefon')->disabled(),
                TextInput::make('school')->label('Sınıf')->disabled(),
                DateTimePicker::make('email_verified_at')->label('E-posta Doğrulama')->disabled(),
                TextInput::make('status')->label('Durum')->disabled(),
                DateTimePicker::make('created_at')->label('Kayıt Tarihi')->disabled(),
                Textarea::make('rejection_reason')->label('Reddetme Sebebi')
                    ->visible(fn ($record) => $record?->status === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Ad Soyad')->searchable()->placeholder('—'),
                TextColumn::make('email')->label('E-posta')->searchable(),
                TextColumn::make('phone')->label('Telefon')->searchable()->placeholder('—'),
                TextColumn::make('school')->label('Sınıf')->searchable()->placeholder('—'),
                IconColumn::make('email_verified_at')->label('Doğrulandı')->boolean()
                    ->trueIcon('heroicon-o-check-badge')->falseIcon('heroicon-o-x-circle'),
                TextColumn::make('assigned_role')->label('Rol')->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ogretmen' => 'Öğretmen', 'veli' => 'Veli', default => '—',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'ogretmen' => 'primary', default => 'gray',
                    }),
                TextColumn::make('status')->label('Durum')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning', 'completed' => 'info', 'approved' => 'success', 'rejected' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'E-posta Bekliyor', 'completed' => 'Kayıt Tamam', 'approved' => 'Onaylandı', 'rejected' => 'Reddedildi',
                    }),
                TextColumn::make('created_at')->label('Kayıt')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'completed' => 'Onay Bekleyen',
                        'approved' => 'Onaylanan',
                        'rejected' => 'Reddedilen',
                    ])
                    ->default('completed'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'completed')
                    ->action(function (PendingUser $record) {
                        $user = User::where('email', $record->email)->first();
                        if (!$user) {
                            Notification::make()->title('Kullanıcı bulunamadı.')->danger()->send();
                            return;
                        }

                        $user->update(['is_active' => true]);
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);

                        try {
                            Mail::to($user->email)->send(new ApprovalMail($user));
                        } catch (\Exception $e) {
                            logger()->warning('Onay maili gönderilemedi: ' . $e->getMessage());
                        }

                        Notification::make()
                            ->title('Kullanıcı onaylandı ve bilgilendirme maili gönderildi.')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'completed')
                    ->form([
                        Textarea::make('rejection_reason')->label('Reddetme Sebebi (isteğe bağlı)'),
                    ])
                    ->action(function (PendingUser $record, array $data) {
                        $user = User::where('email', $record->email)->first();
                        if ($user) {
                            if ($veli = $user->veli) {
                                $veli->delete();
                            }
                            $user->delete();
                        }
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
        ];
    }
}
