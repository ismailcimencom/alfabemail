<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Pages;

use App\Filament\Portal\Resources\Ogretmenler\OgretmenlerResource;
use App\Mail\OgretmenSifreMail;
use App\Models\AktivasyonToken;
use App\Services\ActivityLogger;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateOgretmen extends CreateRecord
{
    protected static string $resource = OgretmenlerResource::class;

    protected function mutateFormData(array $data): array
    {
        $user = auth()->user();
        $okul = $user?->okul;

        $data['okul_id'] = $okul?->id;
        $data['name'] = explode('@', $data['email'])[0];
        $data['is_active'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->assignRole('ogretmen');

        $sinifIds = $this->form->getState()['sinif_ids'] ?? [];
        if (!empty($sinifIds)) {
            $this->record->ogretmen_sinifler_pivot()->sync($sinifIds);
        }

        $token = Str::random(60);
        AktivasyonToken::create([
            'user_id' => $this->record->id,
            'token' => $token,
            'tip' => 'ogretmen_sifre',
            'expires_at' => now()->addHours(24),
        ]);

        try {
            Mail::to($this->record->email)->send(new OgretmenSifreMail($token, $this->record->email));
        } catch (\Exception $e) {
            logger()->warning('Öğretmen davet maili gönderilemedi: ' . $e->getMessage());
        }

        Notification::make()
            ->title('Öğretmen davet edildi')
            ->body($this->record->email . ' adresine şifre belirleme linki gönderildi.')
            ->success()
            ->send();

        ActivityLogger::log('created', 'user', [
            'target_id' => $this->record->id,
            'target_name' => $this->record->name . ' (' . $this->record->email . ')',
            'description' => 'Öğretmen davet edildi: ' . $this->record->name,
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
