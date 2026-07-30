<?php

namespace App\Services;

use App\Models\Ogrenci;
use App\Models\User;
use App\Models\Veli;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentCreationService
{
    public function __construct(
        private MailcowService $mailcow
    ) {}

    public function create(array $data): Ogrenci
    {
        if (!$this->mailcow->isConfigured()) {
            throw new \RuntimeException('Mailcow API yapılandırılmamış.');
        }

        if (!$this->mailcow->testConnection()) {
            throw new \RuntimeException('Mailcow sunucusuna bağlanılamıyor.');
        }

        try {
            $mailbox = $this->mailcow->createStudentMailbox(
                $data['first_name'],
                $data['last_name'],
                $data['nickname'] ?? null,
                100,
                $data['password']
            );
        } catch (\Exception $e) {
            throw new \RuntimeException('Mailcow mailbox oluşturma hatası: ' . $e->getMessage());
        }

        $email = "{$mailbox['local_part']}@" . config('mailcow.domain', 'alfabe.co');

        $user = User::create([
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $email,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);
        $user->assignRole('ogrenci');

        $qrToken = Str::random(32);
        $qrContent = json_encode([
            'email' => $email,
            'password' => $data['password'],
            'token' => $qrToken,
        ]);
        $qrSvg = QrCode::size(400)->generate($qrContent);

        $ogrenci = Ogrenci::create([
            'user_id' => $user->id,
            'sinif_id' => $data['sinif_id'] ?? null,
            'mailbox_local_part' => $mailbox['local_part'],
            'mailbox_quota_mb' => 100,
            'qr_token' => $qrContent,
            'qr_svg' => (string) $qrSvg,
            'anne_email' => $data['anne_email'] ?? null,
            'anne_telefon' => $data['anne_telefon'] ?? null,
            'baba_email' => $data['baba_email'] ?? null,
            'baba_telefon' => $data['baba_telefon'] ?? null,
        ]);

        $veliIds = [];
        foreach (array_filter([$data['anne_email'] ?? null, $data['baba_email'] ?? null]) as $veliEmail) {
            $veliUser = User::firstOrCreate(
                ['email' => $veliEmail],
                ['name' => 'Veli', 'password' => bcrypt('Veli123!'), 'is_active' => true]
            );
            $veliUser->assignRole('veli');
            $veliIds[] = Veli::firstOrCreate(['user_id' => $veliUser->id])->id;
        }

        if (!empty($veliIds)) {
            $ogrenci->veliler()->attach($veliIds);
        }

        return $ogrenci;
    }
}
