<?php

namespace App\Console\Commands;

use App\Models\Ogrenci;
use App\Models\User;
use App\Services\MailcowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SyncMailcowMailboxes extends Command
{
    protected $signature = 'mailcow:sync-mailboxes {--dry-run : Sadece listele, içe aktarma yapma}';
    protected $description = 'Mailcow\'daki mailbox\'ları sistemdeki öğrencilerle senkronize eder';

    public function handle(MailcowService $mailcow): void
    {
        set_time_limit(0);
        if (!$mailcow->isConfigured()) {
            $this->error('Mailcow API yapılandırılmamış.');
            return;
        }

        $this->info('Mailcow mailbox listesi alınıyor...');

        try {
            $mailboxes = $mailcow->listMailboxes();
        } catch (\Exception $e) {
            $this->error("Mailcow bağlantı hatası: {$e->getMessage()}");
            return;
        }

        $existingLocalParts = Ogrenci::whereNotNull('mailbox_local_part')
            ->pluck('mailbox_local_part')
            ->map(fn ($v) => strtolower($v))
            ->toArray();

        $systemLocalParts = [
            'admin', 'info', 'iletisim', 'noreply', 'postmaster',
            'ogrenci', 'ogretmen', 'deneme', 'test',
            'dmarc', 'spam', 'abuse', 'support', 'mailer-daemon',
        ];

        $newMailboxes = [];
        $skipped = 0;
        $skippedSystem = 0;

        foreach ($mailboxes as $mbox) {
            $localPart = is_array($mbox) ? ($mbox['local_part'] ?? '') : '';
            if (empty($localPart)) continue;

            $localPartLower = strtolower($localPart);

            if (in_array($localPartLower, $existingLocalParts)) {
                $skipped++;
                continue;
            }

            if (in_array($localPartLower, $systemLocalParts)) {
                $skippedSystem++;
                continue;
            }

            $newMailboxes[] = $mbox;
        }

        $this->newLine();
        $this->line("Toplam mailbox: " . count($mailboxes));
        $this->line("Sistemde kayıtlı: " . count($existingLocalParts));
        $this->line("Sistem mailbox (atlandı): {$skippedSystem}");
        $this->line("Yeni (içe aktarılacak): " . count($newMailboxes));
        $this->line("Zaten kayıtlı: {$skipped}");

        if (empty($newMailboxes)) {
            $this->info('Tüm mailbox\'lar sistemde kayıtlı.');
            return;
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->warn('DRY RUN — İçe aktarma yapılmadı:');
            foreach ($newMailboxes as $mbox) {
                $name = is_array($mbox) ? ($mbox['name'] ?? 'İsimsiz') : 'İsimsiz';
                $localPart = $mbox['local_part'] ?? '?';
                $this->line("  - {$name} <{$localPart}@" . config('mailcow.domain') . '>');
            }
            return;
        }

        $this->newLine();
        $this->info('Yeni mailbox\'lar içe aktarılıyor...');
        $bar = $this->output->createProgressBar(count($newMailboxes));
        $bar->start();

        $imported = 0;
        $errors = 0;

        foreach ($newMailboxes as $mbox) {
            try {
                $localPart = $mbox['local_part'] ?? '';
                $name = $mbox['name'] ?? $localPart;
                $email = "{$localPart}@" . config('mailcow.domain', 'alfabe.co');

                $password = Str::random(12);

                $mailcow->updateMailboxPassword($email, $password);

                $nameParts = explode(' ', $name, 2);
                $firstName = $nameParts[0] ?? $localPart;
                $lastName = $nameParts[1] ?? '';

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'is_active' => true,
                ]);
                $user->assignRole('ogrenci');

                $qrToken = Str::random(32);
                $qrContent = json_encode([
                    'email' => $email,
                    'password' => $password,
                    'token' => $qrToken,
                ]);
                $qrSvg = QrCode::size(400)->generate($qrContent);

                Ogrenci::create([
                    'user_id' => $user->id,
                    'mailbox_local_part' => $localPart,
                    'mailbox_quota_mb' => 100,
                    'qr_token' => $qrContent,
                    'qr_svg' => (string) $qrSvg,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $mboxLocal = is_array($mbox) ? ($mbox['local_part'] ?? '?') : '?';
                $this->error("\nHata ({$mboxLocal}): {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Tamamlandı: {$imported} içe aktarıldı, {$errors} hata.");
    }
}
