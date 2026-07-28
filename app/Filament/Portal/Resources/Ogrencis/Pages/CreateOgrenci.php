<?php

namespace App\Filament\Portal\Resources\Ogrencis\Pages;

use App\Filament\Portal\Resources\Ogrencis\OgrenciResource;
use Filament\Resources\Pages\CreateRecord;

use App\Models\User;
use App\Models\Ogrenci;
use App\Models\Veli;
use App\Services\MailcowService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CreateOgrenci extends CreateRecord
{
    protected static string $resource = OgrenciResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getSubmitFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        \Illuminate\Support\Facades\Log::info('CreateOgrenci mutateFormDataBeforeCreate', $data);
        
        if (!empty($data['csv_dosya'])) {
            return $data;
        }

        $data['sifre'] = $data['yeni_sifre'] ?? null;
        $data['veli_email'] = $data['veli_email'] ?? null;
        $data['anne_email'] = $data['anne_email'] ?? null;
        $data['baba_email'] = $data['baba_email'] ?? null;
        
        unset($data['user_email'], $data['ad_soyad'], $data['yeni_sifre'], $data['yeni_sifre_tekrar'], $data['sifre_tekrar']);
        
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        if (!empty($data['csv_dosya'])) {
            return $this->handleBulkCreation($data);
        }

        return $this->handleSingleCreation($data);
    }

    protected function handleSingleCreation(array $data): Model
    {
        \Illuminate\Support\Facades\Log::info('handleSingleCreation START', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user()?->email,
            'data_keys' => array_keys($data),
            'first_name' => $data['first_name'] ?? 'MISSING',
            'last_name' => $data['last_name'] ?? 'MISSING',
        ]);

        $mailcow = app(MailcowService::class);

        if (!$mailcow->isConfigured()) {
            Notification::make()
                ->title('Hata')
                ->body('Mailcow API yapılandırılmamış. Lütfen sistem yöneticisiyle iletişime geçin.')
                ->danger()
                ->send();
            $this->halt();
        }

        if (!$mailcow->testConnection()) {
            Notification::make()
                ->title('Hata')
                ->body('Mailcow sunucusuna bağlanılamıyor. Lütfen internet bağlantınızı kontrol edin.')
                ->danger()
                ->send();
            $this->halt();
        }

        $ogrenciEmail = null;
        $mailbox = null;
        
        try {
            \Illuminate\Support\Facades\Log::info('Creating mailbox via Mailcow', [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'nickname' => $data['nickname'] ?? null,
            ]);
            $mailbox = $mailcow->createStudentMailbox(
                $data['first_name'],
                $data['last_name'],
                $data['nickname'] ?? null,
                0,
                $data['sifre'] ?? null
            );
            $ogrenciEmail = "{$mailbox['local_part']}@" . config('mailcow.domain', 'alfabe.co');
        } catch (\Exception $e) {
            Notification::make()
                ->title('E-posta Kutusu Oluşturma Hatası')
                ->body($e->getMessage())
                ->danger()
                ->send();
            $this->halt();
        }

        if (!empty($data['veli_email'])) {
            if (strtolower($data['veli_email']) === strtolower($ogrenciEmail)) {
                try { $mailcow->deleteMailbox($ogrenciEmail); } catch (\Exception $e) {}
                Notification::make()
                    ->title('Hata')
                    ->body('Öğrenci ve veli e-posta adresleri aynı olamaz.')
                    ->danger()
                    ->send();
                $this->halt();
            }
            
            $existingUser = User::whereRaw('LOWER(email) = ?', [strtolower($data['veli_email'])])->first();
            if ($existingUser) {
                try { $mailcow->deleteMailbox($ogrenciEmail); } catch (\Exception $e) {}
                $roleName = $existingUser->getRoleNames()->first() ?? 'kullanıcı';
                Notification::make()
                    ->title('Veli E-posta Hatası')
                    ->body("Bu e-posta zaten {$roleName} olarak kayıtlı.")
                    ->danger()
                    ->send();
                $this->halt();
            }
        }

        foreach (['anne_email', 'baba_email'] as $emailField) {
            if (!empty($data[$emailField])) {
                $existingUser = User::whereRaw('LOWER(email) = ?', [strtolower($data[$emailField])])->first();
                if ($existingUser) {
                    try { $mailcow->deleteMailbox($ogrenciEmail); } catch (\Exception $e) {}
                    $fieldLabel = $emailField === 'anne_email' ? 'Anne' : 'Baba';
                    $roleName = $existingUser->getRoleNames()->first() ?? 'kullanıcı';
                    Notification::make()
                        ->title("{$fieldLabel} E-posta Hatası")
                        ->body("{$fieldLabel} e-posta zaten {$roleName} olarak kayıtlı.")
                        ->danger()
                        ->send();
                    $this->halt();
                }
            }
        }

        return DB::transaction(function () use ($data, $mailbox, $ogrenciEmail) {
            $user = User::create([
                'name' => "{$data['first_name']} {$data['last_name']}",
                'email' => $ogrenciEmail,
                'password' => Hash::make($data['sifre']),
                'is_active' => true,
            ]);
            $user->assignRole('ogrenci');

            $veliIds = [];
            $veliEmails = array_filter([
                $data['veli_email'] ?? null,
                $data['anne_email'] ?? null,
                $data['baba_email'] ?? null
            ]);

            foreach ($veliEmails as $veliEmail) {
                $veliUser = User::firstOrCreate(
                    ['email' => $veliEmail],
                    ['name' => 'Veli', 'password' => bcrypt('Veli123!'), 'is_active' => true]
                );
                $veliUser->assignRole('veli');
                
                $veli = Veli::firstOrCreate(['user_id' => $veliUser->id]);
                $veliIds[] = $veli->id;
            }

            $qrToken = Str::random(32);
            $qrContent = json_encode([
                'email' => $user->email,
                'password' => $data['sifre'],
                'token' => $qrToken
            ]);
            $qrSvg = QrCode::size(400)->generate($qrContent);

            $ogrenci = Ogrenci::create([
                'user_id' => $user->id,
                'sinif_id' => $data['sinif_id'],
                'mailbox_local_part' => $mailbox['local_part'],
                'qr_token' => $qrContent,
                'qr_svg' => (string) $qrSvg,
                'anne_email' => $data['anne_email'] ?? null,
                'baba_email' => $data['baba_email'] ?? null,
                'veli_email' => $data['veli_email'] ?? null,
            ]);

            if (!empty($veliIds)) {
                $ogrenci->veliler()->attach($veliIds);
            }

            Notification::make()
                ->title('Başarılı')
                ->body("Öğrenci {$user->name} başarıyla oluşturuldu. E-posta: {$ogrenciEmail}")
                ->success()
                ->send();

            return $ogrenci;
        });
    }

    protected function handleBulkCreation(array $data): Model
    {
        $file = $data['csv_dosya'];
        $path = storage_path('app/' . $file);
        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);
        
        $sinifId = $data['sinif_id'] ?? null;
        $created = null;

        foreach ($rows as $row) {
            if (count($row) < 2) continue;
            
            $rowData = array_combine($header, $row);
            $firstName = trim($rowData['ad'] ?? '');
            $lastName = trim($rowData['soyad'] ?? '');
            $veliEmail = trim($rowData['veli_email'] ?? '');

            if (empty($firstName) || empty($lastName)) continue;

            try {
                $mailcow = app(MailcowService::class);
                $mailbox = $mailcow->createStudentMailbox($firstName, $lastName, null);

                $user = User::create([
                    'name' => "{$firstName} {$lastName}",
                    'email' => "{$mailbox['local_part']}@" . config('mailcow.domain', 'alfabe.co'),
                    'password' => $mailbox['password'],
                    'is_active' => true,
                ]);
                $user->assignRole('ogrenci');

                if (!empty($veliEmail)) {
                    $existingVeli = User::whereRaw('LOWER(email) = ?', [strtolower($veliEmail)])->first();
                    if ($existingVeli) {
                        $roleName = $existingVeli->getRoleNames()->first() ?? 'kullanıcı';
                        throw new \Exception("Veli e-posta ({$veliEmail}) zaten {$roleName} olarak kayıtlı.");
                    }

                    $veliUser = User::firstOrCreate(
                        ['email' => $veliEmail],
                        ['name' => 'Veli', 'password' => bcrypt('Veli123!'), 'is_active' => true]
                    );
                    $veliUser->assignRole('veli');
                    Veli::firstOrCreate(['user_id' => $veliUser->id]);
                }

                $qrToken = Str::random(32);
                $qrContent = json_encode([
                    'email' => $user->email,
                    'password' => $mailbox['password'],
                    'token' => $qrToken
                ]);
                $qrSvg = QrCode::size(400)->generate($qrContent);

                $created = Ogrenci::create([
                    'user_id' => $user->id,
                    'sinif_id' => $sinifId,
                    'mailbox_local_part' => $mailbox['local_part'],
                    'qr_token' => $qrContent,
                    'qr_svg' => (string) $qrSvg,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Ogrenci oluşturma hatası: ' . $e->getMessage());
                throw new \Exception('Öğrenci oluşturulamadı: ' . $e->getMessage());
            }
        }

        return $created ?? new Ogrenci();
    }

    protected function afterCreate(): void
    {
        $sinif = $this->record->sinif;
        $ogretmen = auth()->user();
        
        \App\Services\ActivityLogger::created($this->record, 
            'Öğrenci eklendi: ' . $this->record->user?->name . 
            ' - Sınıf: ' . ($sinif?->ad ?? 'Belirsiz') .
            ' - Öğretmen: ' . ($ogretmen?->name ?? 'Belirsiz')
        );
    }
}