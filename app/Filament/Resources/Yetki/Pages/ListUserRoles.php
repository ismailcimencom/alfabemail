<?php

namespace App\Filament\Resources\Yetki\Pages;

use App\Models\User;
use Filament\Resources\Pages\ListRecords as BaseListRecords;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;

class ListUserRoles extends BaseListRecords
{
    protected static ?string $model = User::class;
    protected static ?string $title = 'Kullanıcı Roller';
    protected static string $resource = \App\Filament\Resources\Yetki\YetkiManagement::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('rol_ata')
                ->label('Kullanıcıya Rol Ata')
                ->icon('heroicon-o-plus')
                ->modalTitle('Kullanıcıya Rol Ata')
                ->form([
                    Select::make('user_id')
                        ->label('Kullanıcı')
                        ->options(fn () => User::whereNotNull('email')->pluck('name', 'id'))
                        ->required(),
                    Select::make('role_id')
                        ->label('Rol')
                        ->options(fn () => Role::where('name', '!=', 'yonetici')->pluck('name', 'id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    $user = User::find($data['user_id']);
                    $role = Role::find($data['role_id']);
                    
                    if ($user && $role) {
                        $user->syncRoles([$role->name]);
                        Notification::make()
                            ->title('Başarılı')
                            ->body($user->name . ' rolü: ' . $role->name . ' olarak güncellendi.')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->whereNotNull('email'))
            ->columns([
                TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color('info'),
                BadgeColumn::make('is_active')
                    ->label('Durum')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Pasif'),
                TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->label('Rol Değiştir')
                    ->modalTitle(fn ($record) => $record->name . ' - Rol Değiştir')
                    ->form([
                        Select::make('role_id')
                            ->label('Yeni Rol')
                            ->options(fn () => Role::where('name', '!=', 'yonetici')->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->fillForm(fn ($record) => [
                        'role_id' => $record->roles()->first()?->id,
                    ])
                    ->action(function ($record, array $data) {
                        $role = Role::find($data['role_id']);
                        if ($role) {
                            $record->syncRoles([$role->name]);
                            Notification::make()
                                ->title('Başarılı')
                                ->body('Rol güncellendi: ' . $role->name)
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->paginated(false);
    }
}