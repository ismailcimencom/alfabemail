<?php

namespace App\Filament\Portal\Resources\Sinifs\Pages;

use App\Filament\Portal\Resources\Sinifs\SinifResource;
use App\Mail\OgretmenSifreMail;
use App\Models\AktivasyonToken;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EditSinif extends EditRecord
{
    protected static string $resource = SinifResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $isYonetici = $user?->hasAnyRole(['admin', 'yonetici']) ?? false;

        return [
            Action::make('ogretmen_ekle')
                ->label('Öğretmen Davet Et')
                ->icon('heroicon-o-user-plus')
                ->color('info')
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false)
                ->form([
                    TextInput::make('email')
                        ->label('Öğretmen E-posta')
                        ->email()
                        ->required()
                        ->placeholder('ornek@okul.com'),
                ])
                ->action(function (array $data) {
                    $sinif = $this->record;
                    $email = $data['email'];
                    $ogretmen = User::where('email', $email)->first();

                    if ($ogretmen) {
                        if (!$ogretmen->hasRole('ogretmen')) {
                            Notification::make()->title('Bu kullanıcı öğretmen değil.')->danger()->send();
                            return;
                        }

                        $exists = $sinif->ogretmenler()->where('ogretmen_user_id', $ogretmen->id)->exists();
                        if ($exists) {
                            Notification::make()->title('Bu öğretmen zaten sınıfta.')->warning()->send();
                            return;
                        }

                        $sinif->ogretmenler()->attach($ogretmen->id);
                        Notification::make()
                            ->title('Öğretmen sınıfa eklendi.')
                            ->success()
                            ->send();
                    } else {
                        $okul = $sinif->okul;
                        $ogretmen = User::create([
                            'name' => explode('@', $email)[0],
                            'email' => $email,
                            'okul_id' => $okul?->id,
                            'is_active' => true,
                        ]);
                        $ogretmen->assignRole('ogretmen');
                        $sinif->ogretmenler()->attach($ogretmen->id);

                        $token = Str::random(60);
                        AktivasyonToken::create([
                            'user_id' => $ogretmen->id,
                            'token' => $token,
                            'tip' => 'ogretmen_sifre',
                            'expires_at' => now()->addHours(24),
                        ]);

                        try {
                            Mail::to($email)->send(new OgretmenSifreMail($token, $email));
                        } catch (\Exception $e) {
                            logger()->warning('Öğretmen davet maili gönderilemedi: ' . $e->getMessage());
                        }

                        Notification::make()
                            ->title('Öğretmen davet edildi')
                            ->body("{$email} adresine şifre belirleme linki gönderildi.")
                            ->success()
                            ->send();
                    }
                }),

            Action::make('ogretmen_cikar')
                ->label('Öğretmen Çıkar')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->visible($isYonetici)
                ->form([
                    Select::make('ogretmen_id')
                        ->label('Öğretmen')
                        ->options(fn () => $this->record->ogretmenler->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $sinif = $this->record;
                    $sinif->ogretmenler()->detach($data['ogretmen_id']);
                    Notification::make()
                        ->title('Öğretmen sınıftan çıkarıldı.')
                        ->success()
                        ->send();
                }),

            DeleteAction::make()
                ->visible($isYonetici),
        ];
    }
}
