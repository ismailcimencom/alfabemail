<?php

namespace App\Filament\Resources\Yetki\Pages;

use App\Services\PermissionService;
use Filament\Resources\Pages\ListRecords as BaseListRecords;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ListRoles extends BaseListRecords
{
    protected static ?string $model = Role::class;
    protected static ?string $title = 'Roller ve İzinler';
    protected static string $resource = \App\Filament\Resources\Yetki\YetkiManagement::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(Role::where('name', '!=', 'yonetici'))
            ->columns([
                TextColumn::make('name')
                    ->label('Rol Adı')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'admin' => '🔧 Admin',
                        'ogretmen' => '🧑‍🏫 Öğretmen',
                        'veli' => '👨‍👩‍👧 Veli',
                        'ogrenci' => '🎒 Öğrenci',
                        default => $state,
                    }),
                TextColumn::make('permissions_count')
                    ->label('İzin Sayısı')
                    ->counts('permissions'),
                TextColumn::make('users_count')
                    ->label('Kullanıcı Sayısı')
                    ->counts('users'),
                TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->label('İzinleri Düzenle')
                    ->modalTitle(fn ($record) => $this->getRoleLabel($record->name) . ' - İzinler')
                    ->form(fn ($record) => $this->getPermissionForm())
                    ->fillForm(fn ($record) => [
                        'permissions' => $record->permissions()->pluck('name')->toArray(),
                    ])
                    ->action(function ($record, array $data) {
                        $permissions = Permission::whereIn('name', $data['permissions'] ?? [])->get();
                        $record->syncPermissions($permissions);
                        
                        app()[PermissionRegistrar::class]->forgetCachedPermissions();
                        
                        Notification::make()
                            ->title('Başarılı')
                            ->body($this->getRoleLabel($record->name) . ' için izinler güncellendi.')
                            ->success()
                            ->send();
                    }),
            ])
            ->paginated(false);
    }

    private function getRoleLabel(string $name): string
    {
        return match ($name) {
            'admin' => '🔧 Admin',
            'ogretmen' => '🧑‍🏫 Öğretmen',
            'veli' => '👨‍👩‍👧 Veli',
            'ogrenci' => '🎒 Öğrenci',
            default => $name,
        };
    }

    private function getPermissionForm(): array
    {
        $groups = PermissionService::getGroups();
        $sections = [];

        foreach ($groups as $groupKey => $group) {
            $options = [];
            
            // Normal izinler
            foreach ($group['permissions'] as $perm) {
                $permName = "{$groupKey}.{$perm}";
                $options[$permName] = ucfirst($perm);
            }

            // Sistem özel izinleri
            if (isset($group['custom_names'])) {
                foreach ($group['custom_names'] as $permName => $label) {
                    $options[$permName] = $label;
                }
            }

            if (!empty($options)) {
                $sections[] = Section::make($group['label'])
                    ->schema([
                        CheckboxList::make('permissions')
                            ->options($options)
                            ->columns(2)
                            ->inline(),
                    ]);
            }
        }

        return $sections;
    }
}