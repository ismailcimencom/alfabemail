<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Pages;

use App\Filament\Portal\Resources\Ogretmenler\OgretmenlerResource;
use App\Services\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditOgretmen extends EditRecord
{
    protected static string $resource = OgretmenlerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormData(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        unset($data['password_confirmation']);
        return $data;
    }

    protected function afterSave(): void
    {
        $sinifIds = $this->form->getState()['sinif_ids'] ?? [];
        $this->record->ogretmen_sinifler_pivot()->sync($sinifIds);

        ActivityLogger::log('updated', 'user', [
            'target_id' => $this->record->id,
            'target_name' => $this->record->name . ' (' . $this->record->email . ')',
            'description' => 'Öğretmen düzenlendi: ' . $this->record->name,
        ]);
    }
}
