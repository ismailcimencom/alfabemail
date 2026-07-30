<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\AdminApproval;
use App\Models\User;
use App\Models\Veli;
use App\Services\ActivityLogger;
use App\Services\StudentCreationService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?array $ogrenciData = null;
    private ?array $ogretmenData = null;
    private ?array $veliData = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $ogrenciRoleId = Role::where('name', 'ogrenci')->value('id');
        if ($ogrenciRoleId && ($data['roles'] ?? null) == $ogrenciRoleId) {
            $this->ogrenciData = [
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'nickname' => $data['nickname'] ?? null,
                'sinif_id' => $data['sinif_id'] ?? null,
                'anne_email' => $data['anne_email'] ?? null,
                'anne_telefon' => $data['anne_telefon'] ?? null,
                'baba_email' => $data['baba_email'] ?? null,
                'baba_telefon' => $data['baba_telefon'] ?? null,
            ];

            $data['name'] = trim($this->ogrenciData['first_name'] . ' ' . $this->ogrenciData['last_name']);
            $data['email'] = null;

            unset($data['first_name'], $data['last_name'], $data['nickname'], $data['sinif_id'], $data['anne_email'], $data['anne_telefon'], $data['baba_email'], $data['baba_telefon']);
        }

        $ogretmenRoleId = Role::where('name', 'ogretmen')->value('id');
        if ($ogretmenRoleId && ($data['roles'] ?? null) == $ogretmenRoleId) {
            $this->ogretmenData = [
                'sinif_ids' => $data['sinif_ids'] ?? [],
            ];
            unset($data['sinif_ids']);
        }

        $veliRoleId = Role::where('name', 'veli')->value('id');
        if ($veliRoleId && ($data['roles'] ?? null) == $veliRoleId) {
            $this->veliData = [
                'ogrenci_ids' => $data['ogrenci_ids'] ?? [],
                'veli_type' => $data['veli_type'] ?? null,
            ];
            unset($data['ogrenci_ids'], $data['veli_type']);
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        if ($this->ogrenciData === null) {
            return parent::handleRecordCreation($data);
        }

        try {
            $ogrenci = app(StudentCreationService::class)->create([
                'first_name' => $this->ogrenciData['first_name'],
                'last_name' => $this->ogrenciData['last_name'],
                'nickname' => $this->ogrenciData['nickname'],
                'sinif_id' => $this->ogrenciData['sinif_id'],
                'password' => $data['password'],
                'anne_email' => $this->ogrenciData['anne_email'],
                'anne_telefon' => $this->ogrenciData['anne_telefon'],
                'baba_email' => $this->ogrenciData['baba_email'],
                'baba_telefon' => $this->ogrenciData['baba_telefon'],
            ]);

            $user = $ogrenci->user;

            Notification::make()
                ->title('Başarılı')
                ->body("Öğrenci {$user->name} başarıyla oluşturuldu. E-posta: {$user->email}")
                ->success()
                ->send();

            ActivityLogger::created($user, 'Öğrenci oluşturuldu (Admin): ' . $user->name . ' - E-posta: ' . $user->email);

            return $user->load('ogrenci');
        } catch (\RuntimeException $e) {
            Notification::make()->title('Hata')->body($e->getMessage())->danger()->send();
            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        if ($this->ogrenciData !== null) {
            return;
        }

        if ($this->ogretmenData !== null) {
            if (!empty($this->ogretmenData['sinif_ids'])) {
                $this->record->ogretmen_sinifler_pivot()->sync($this->ogretmenData['sinif_ids']);
            }
        }

        if ($this->veliData !== null) {
            $veli = Veli::firstOrCreate(['user_id' => $this->record->id]);
            if (!empty($this->veliData['veli_type'])) {
                $veli->veli_type = $this->veliData['veli_type'];
                $veli->save();
            }
            if (!empty($this->veliData['ogrenci_ids'])) {
                $veli->ogrenciler()->sync($this->veliData['ogrenci_ids']);
            }
        }

        if ($this->record->hasRole('admin')) {
            $otherAdmins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))
                ->where('id', '!=', $this->record->id)
                ->get();

            if ($otherAdmins->isNotEmpty()) {
                $this->record->update(['is_active' => false]);

                foreach ($otherAdmins as $admin) {
                    AdminApproval::create([
                        'target_user_id' => $this->record->id,
                        'approver_user_id' => $admin->id,
                    ]);
                }

                Notification::make()
                    ->title('Admin onayı bekliyor')
                    ->body("Diğer adminlerin onayından sonra {$this->record->name} giriş yapabilir.")
                    ->warning()
                    ->send();
            }
        }

        ActivityLogger::created($this->record, 'Kullanıcı oluşturuldu: ' . $this->record->name);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Oluştur')
                ->submit('create'),
            Action::make('cancel')
                ->label('İptal')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
