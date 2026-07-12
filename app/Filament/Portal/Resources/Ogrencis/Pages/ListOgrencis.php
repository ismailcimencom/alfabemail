<?php

namespace App\Filament\Portal\Resources\Ogrencis\Pages;

use App\Filament\Portal\Resources\Ogrencis\OgrenciResource;
use App\Models\Ogrenci;
use App\Models\Sinif;
use App\Models\User;
use App\Services\MailcowService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Actions\Action as NotificationAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ListOgrencis extends ListRecords
{
    protected static string $resource = OgrenciResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_student')
                ->label('Yeni Öğrenci Ekle')
                ->url(fn () => static::getResource()::getUrl('create'))
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false),
            Action::make('toplu_ogrenci_ekle')
                ->label('Toplu Öğrenci Ekle')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('success')
                ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false)
                ->modalHeading('Toplu Öğrenci Ekle')
                ->modalDescription('Öğrenci listesini Excel\'den kopyalayıp aşağıya yapıştırın. Sütunlar: Ad, Soyad, Şifre (opsiyonel).')
                ->modalSubmitActionLabel('Parse Et ve Önizle')
                ->form([
                    Select::make('sinif_id')
                        ->label('Sınıf')
                        ->options(function () {
                            $user = auth()->user();
                            if ($user?->hasRole('ogretmen')) {
                                return Sinif::whereHas('ogretmenler', fn($q) => $q->where('users.id', $user->id))
                                    ->pluck('ad', 'id');
                            }
                            if ($user?->hasRole('yonetici')) {
                                return Sinif::whereHas('okul', fn($q) => $q->where('yonetici_user_id', $user->id))
                                    ->pluck('ad', 'id');
                            }
                            return Sinif::pluck('ad', 'id');
                        })
                        ->searchable()
                        ->required(),
                    Textarea::make('veri')
                        ->label('Öğrenci Listesi')
                        ->placeholder(
                            "Ahmet\tYılmaz\tAlfabe123!\nAyşe\tDemir\tAlfabe123!\nMehmet\tKaya\tAlfabe123!"
                        )
                        ->rows(10)
                        ->helperText('Sütunları tab/virgül/noktalı virgül ile ayırın. Sıra: Ad, Soyad, Şifre (opsiyonel, boşsa Alfabe123! atanır).')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $sinifId = $data['sinif_id'];
                    $text = $data['veri'];

                    $mailcow = app(MailcowService::class);
                    $parsed = $this->parseText($text, $mailcow);

                    $validRows = $parsed['valid'];
                    $errors = $parsed['errors'];

                    if (empty($validRows)) {
                        $msg = 'Hiç geçerli kayıt bulunamadı.';
                        if (!empty($errors)) {
                            $msg .= '<br>' . implode('<br>', array_slice($errors, 0, 5));
                        }
                        Notification::make()->title('Hata')->danger()->body($msg)->send();
                        return;
                    }

                    session(['import_preview' => [
                        'sinif_id' => $sinifId,
                        'rows' => $validRows,
                    ]]);

                    $errorMsg = !empty($errors)
                        ? '<br><br><strong>Uyarılar:</strong><br>' . implode('<br>', array_slice($errors, 0, 5))
                        : '';

                    Notification::make()
                        ->title(count($validRows) . ' öğrenci oluşturulacak')
                        ->body('Onaylıyor musunuz?' . $errorMsg)
                        ->success()
                        ->actions([
                            NotificationAction::make('confirmImport')
                                ->label(count($validRows) . ' Öğrenciyi Oluştur')
                                ->color('success')
                                ->button()
                                ->action(fn () => $this->confirmImport()),
                        ])
                        ->send();
                }),
            Action::make('sync_mailcow')
                ->label('Mailcow\'u Senkronize Et')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                ->requiresConfirmation()
                ->modalHeading('Mailcow Senkronizasyonu')
                ->modalDescription('Mailcow\'da olup sistemde olmayan tüm mailbox\'lar öğrenci olarak içe aktarılır. Sistem mailbox\'ları (admin, info, vb.) atlanır.')
                ->modalSubmitActionLabel('Senkronize Et')
                ->action(function () {
                    try {
                        $mailcow = app(MailcowService::class);
                        if (!$mailcow->isConfigured()) {
                            Notification::make()->title('Mailcow API yapılandırılmamış.')->danger()->send();
                            return;
                        }

                        $mailboxes = $mailcow->listMailboxes();

                        $existingLocalParts = Ogrenci::whereNotNull('mailbox_local_part')
                            ->pluck('mailbox_local_part')
                            ->map(fn ($v) => strtolower($v))
                            ->toArray();

                        $systemLocalParts = [
                            'admin', 'info', 'iletisim', 'noreply', 'postmaster',
                            'ogrenci', 'ogretmen', 'yonetici', 'deneme', 'test',
                            'dmarc', 'spam', 'abuse', 'support', 'mailer-daemon',
                        ];

                        $imported = 0;
                        $errors = 0;

                        foreach ($mailboxes as $mbox) {
                            $localPart = is_array($mbox) ? ($mbox['local_part'] ?? '') : '';
                            if (empty($localPart)) continue;

                            $localPartLower = strtolower($localPart);
                            if (in_array($localPartLower, $existingLocalParts)) continue;
                            if (in_array($localPartLower, $systemLocalParts)) continue;

                            try {
                                $name = $mbox['name'] ?? $localPart;
                                $email = "{$localPart}@" . config('mailcow.domain', 'alfabe.co');
                                $password = Str::random(12);

                                $mailcow->updateMailboxPassword($email, $password);

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
                                    'qr_token' => $qrContent,
                                    'qr_svg' => (string) $qrSvg,
                                ]);

                                $imported++;
                            } catch (\Exception $e) {
                                $errors++;
                            }
                        }

                        if ($imported > 0) {
                            Notification::make()
                                ->title("{$imported} yeni öğrenci içe aktarıldı.")
                                ->body($errors > 0 ? "{$errors} hata oluştu." : null)
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Aktarılacak yeni mailbox bulunamadı.')
                                ->body('Sistem mailbox\'ları (admin, info, vb.) otomatik atlanır.')
                                ->info()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Senkronizasyon hatası')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function confirmImport(): void
    {
        $preview = session('import_preview');

        if (!$preview || empty($preview['rows'])) {
            Notification::make()->title('Veri bulunamadı. Lütfen listeyi tekrar yapıştırın.')->danger()->send();
            return;
        }

        $sinifId = $preview['sinif_id'];
        $rows = $preview['rows'];
        $mailcow = app(MailcowService::class);
        $domain = config('mailcow.domain', 'alfabe.co');

        $imported = 0;
        $errors = 0;
        $errorDetails = [];

        foreach ($rows as $row) {
            try {
                $mailbox = $mailcow->createStudentMailbox(
                    $row['ad'],
                    $row['soyad'],
                    $row['nickname'],
                    100,
                    $row['sifre']
                );

                $email = "{$mailbox['local_part']}@{$domain}";

                $user = User::create([
                    'name' => $row['ad'] . ' ' . $row['soyad'],
                    'email' => $email,
                    'password' => Hash::make($row['sifre']),
                    'is_active' => true,
                ]);
                $user->assignRole('ogrenci');

                $qrToken = Str::random(32);
                $qrContent = json_encode([
                    'email' => $email,
                    'password' => $row['sifre'],
                    'token' => $qrToken,
                ]);
                $qrSvg = QrCode::size(400)->generate($qrContent);

                Ogrenci::create([
                    'user_id' => $user->id,
                    'sinif_id' => $sinifId,
                    'mailbox_local_part' => $mailbox['local_part'],
                    'mailbox_quota_mb' => 100,
                    'qr_token' => $qrContent,
                    'qr_svg' => (string) $qrSvg,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors++;
                $errorDetails[] = $row['ad'] . ' ' . $row['soyad'] . ' — ' . $e->getMessage();
            }
        }

        session()->forget('import_preview');

        if ($imported > 0) {
            Notification::make()
                ->title("{$imported} öğrenci başarıyla oluşturuldu.")
                ->body($errors > 0 ? "{$errors} hata oluştu." : null)
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Hiç öğrenci oluşturulamadı.')
                ->body(implode("\n", array_slice($errorDetails, 0, 5)))
                ->danger()
                ->send();
        }

        if (!empty($errorDetails)) {
            \Illuminate\Support\Facades\Log::warning('Toplu öğrenci oluşturma hataları: ' . implode('; ', $errorDetails));
        }
    }

    private function parseText(string $text, MailcowService $mailcow): array
    {
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text);

        $lines = explode("\n", str_replace("\r\n", "\n", $text));
        $lines = array_filter($lines, fn($l) => trim($l) !== '');
        $lines = array_values($lines);

        if (empty($lines)) {
            return ['valid' => [], 'errors' => ['Hiç veri girilmedi.']];
        }

        $delimiters = ["\t", ',', ';', '|'];
        $headerMap = [];
        $dataStartIndex = 0;

        $cols = null;
        foreach ($delimiters as $delim) {
            $parts = str_getcsv($lines[0], $delim);
            if (count($parts) >= 2) {
                $cols = $parts;
                break;
            }
        }
        if ($cols === null) {
            $cols = preg_split('/\s+/', $lines[0]);
        }

        foreach ($cols as $i => $col) {
            $colLower = mb_strtolower(trim((string)$col));
            if (in_array($colLower, ['ad', 'adı', 'first name', 'firstname', 'isim'])) $headerMap['ad'] = $i;
            if (in_array($colLower, ['soyad', 'soyadı', 'last name', 'lastname', 'soyisim'])) $headerMap['soyad'] = $i;
            if (in_array($colLower, ['şifre', 'sifre', 'password', 'parola'])) $headerMap['sifre'] = $i;
        }

        if (isset($headerMap['ad']) && isset($headerMap['soyad'])) {
            $dataStartIndex = 1;
        } else {
            $headerMap['ad'] = 0;
            $headerMap['soyad'] = 1;
            $dataStartIndex = 0;
        }

        if (count($lines) <= $dataStartIndex) {
            return ['valid' => [], 'errors' => ['En az bir öğrenci satırı girin.']];
        }

        $domain = config('mailcow.domain', 'alfabe.co');
        $valid = [];
        $errors = [];

        for ($i = $dataStartIndex; $i < count($lines); $i++) {
            $row = null;
            foreach ($delimiters as $delim) {
                $parts = str_getcsv($lines[$i], $delim);
                if (count($parts) >= 2) {
                    $row = $parts;
                    break;
                }
            }
            if ($row === null) {
                $row = preg_split('/\s+/', $lines[$i]);
            }

            $rowNum = $i + 1;

            $filtered = array_filter($row, fn($v) => trim((string)$v) !== '');
            if (empty($filtered)) continue;

            $ad = trim((string)($row[$headerMap['ad']] ?? ''));
            $soyad = trim((string)($row[$headerMap['soyad']] ?? ''));

            if (empty($ad) || empty($soyad)) {
                $errors[] = "Satır {$rowNum}: Ad veya Soyad eksik.";
                continue;
            }

            $sifre = isset($headerMap['sifre']) ? trim((string)($row[$headerMap['sifre']] ?? '')) : '';
            if (empty($sifre)) {
                $sifre = 'Alfabe123!';
            }

            $slugDot = $mailcow->slugify($ad . '.' . $soyad);
            $emailDot = "{$slugDot}@{$domain}";

            if (!$this->emailExists($emailDot, $slugDot)) {
                $valid[] = [
                    'ad' => $ad,
                    'soyad' => $soyad,
                    'sifre' => $sifre,
                    'nickname' => null,
                    'email' => $emailDot,
                ];
                continue;
            }

            $slugFlat = $mailcow->slugify($ad . $soyad);
            $emailFlat = "{$slugFlat}@{$domain}";

            if (!$this->emailExists($emailFlat, $slugFlat)) {
                $valid[] = [
                    'ad' => $ad,
                    'soyad' => $soyad,
                    'sifre' => $sifre,
                    'nickname' => $slugFlat,
                    'email' => $emailFlat,
                ];
                continue;
            }

            $errors[] = "Satır {$rowNum}: {$ad} {$soyad} — <strong>{$emailDot}</strong> ve <strong>{$emailFlat}</strong> dolu. Lütfen farklı bir ad deneyin.";
        }

        return ['valid' => $valid, 'errors' => $errors];
    }

    private function emailExists(string $email, string $localPart): bool
    {
        return User::where('email', $email)->exists()
            || Ogrenci::where('mailbox_local_part', $localPart)->exists();
    }
}
