<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\Veli;
use App\Services\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    private array $ogretmenSinifIds = [];
    private array $veliOgrenciIds = [];
    private ?string $veli_type = null;
    private array $ogrenciFields = [];

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $ogrenci = $this->record->ogrenci;
        if ($ogrenci) {
            $data['sinif_id'] = $ogrenci->sinif_id;
            $data['anne_email'] = $ogrenci->anne_email;
            $data['anne_telefon'] = $ogrenci->anne_telefon;
            $data['baba_email'] = $ogrenci->baba_email;
            $data['baba_telefon'] = $ogrenci->baba_telefon;
            $data['ogrenci_okul_id'] = $ogrenci->sinif?->okul_id;
        }

        $data['sinif_ids'] = $this->record->ogretmen_sinifler_pivot->pluck('id')->toArray();
        $data['ogrenci_ids'] = $this->record->veli?->ogrenciler->pluck('id')->toArray() ?? [];
        $data['veli_type'] = $this->record->veli?->veli_type;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->ogretmenSinifIds = $data['sinif_ids'] ?? [];
        $this->veliOgrenciIds = $data['ogrenci_ids'] ?? [];
        $this->veli_type = $data['veli_type'] ?? null;
        $this->ogrenciFields = [
            'sinif_id' => $data['sinif_id'] ?? null,
            'anne_email' => $data['anne_email'] ?? null,
            'anne_telefon' => $data['anne_telefon'] ?? null,
            'baba_email' => $data['baba_email'] ?? null,
            'baba_telefon' => $data['baba_telefon'] ?? null,
        ];
        unset($data['sinif_ids'], $data['ogrenci_ids'], $data['veli_type'], $data['sinif_id'], $data['anne_email'], $data['anne_telefon'], $data['baba_email'], $data['baba_telefon'], $data['ogrenci_okul_id']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->hasRole('ogretmen')) {
            $this->record->ogretmen_sinifler_pivot()->sync($this->ogretmenSinifIds);
        }

        if ($this->record->hasRole('veli')) {
            $veli = Veli::firstOrCreate(['user_id' => $this->record->id]);
            if ($this->veli_type) {
                $veli->veli_type = $this->veli_type;
                $veli->save();
            }
            $veli->ogrenciler()->sync($this->veliOgrenciIds);
        }

        if ($this->record->hasRole('ogrenci') && $this->record->ogrenci) {
            $this->record->ogrenci->update($this->ogrenciFields);
        }

        ActivityLogger::log('updated', 'user', [
            'target_id' => $this->record->id,
            'description' => 'Kullanıcı düzenlendi: ' . $this->record->name,
        ]);
    }
}
