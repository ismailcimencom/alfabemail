<?php

namespace App\Filament\Portal\Resources\Sinifs\Pages;

use App\Filament\Portal\Resources\Sinifs\SinifResource;
use App\Services\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions\Action;

class CreateSinif extends CreateRecord
{
    protected static string $resource = SinifResource::class;

    public function mount(): void
    {
        $user = auth()->user();
        if (!$user || !$user->hasAnyRole(['admin', 'ogretmen'])) {
            $this->redirect(SinifResource::getUrl('index'));
            return;
        }

        parent::mount();
    }

    protected function afterCreate(): void
    {
        $user = auth()->user();
        $sinif = $this->record;
        $ogretmenler = $this->data['ogretmenler'] ?? [];

        if ($user->hasRole('ogretmen')) {
            $ogretmenler[] = $user->id;
        }

        if (!empty($ogretmenler) && is_array($ogretmenler)) {
            $sinif->ogretmenler()->sync(array_unique($ogretmenler));
        }

        ActivityLogger::created($sinif, 'Sınıf oluşturuldu: ' . $sinif->ad);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $data['durum'] = 'aktif';
        }

        if ($user->hasRole('ogretmen')) {
            $data['durum'] = 'beklemede';
        }

        return $data;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Oluştur')
                ->submit('create'),
            Action::make('cancel')
                ->label('İptal')
                ->url($this->previousUrl ?? static::getUrl())
                ->color('gray'),
        ];
    }
}
