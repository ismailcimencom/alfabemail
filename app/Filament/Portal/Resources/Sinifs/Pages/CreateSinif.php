<?php

namespace App\Filament\Portal\Resources\Sinifs\Pages;

use App\Filament\Portal\Resources\Sinifs\SinifResource;
use App\Models\Okul;
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
        if (!$user || !$user->hasAnyRole(['admin', 'yonetici', 'ogretmen'])) {
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

        if ($user->hasAnyRole(['admin', 'yonetici']) && $sinif->okul) {
            $okul = $sinif->okul;
            if (!$okul->yonetici_user_id) {
                $okul->yonetici_user_id = $user->id;
                $okul->save();
            }
            if (!$user->okul_id) {
                $user->okul_id = $okul->id;
                $user->save();
            }
        }

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

        if ($user->hasAnyRole(['admin', 'yonetici'])) {
            if (!isset($data['okul_id']) || empty($data['okul_id'])) {
                $okul = Okul::where('yonetici_user_id', $user->id)->first();
                $data['okul_id'] = $okul?->id;
                if (!$data['okul_id']) {
                    Notification::make()
                        ->title('Hata')
                        ->body('Bu yöneticiye ait okul bulunamadı.')
                        ->danger()
                        ->send();
                    $this->halt();
                }
            }
            $data['durum'] = 'aktif';
        }

        if ($user->hasRole('ogretmen')) {
            $data['okul_id'] = $user->okul_id;
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
