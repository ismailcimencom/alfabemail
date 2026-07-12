<?php

namespace App\Filament\Portal\Resources\Ogrencis\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

use Filament\Forms\Components\TextInput;
use App\Models\Ogrenci;
use App\Services\MailcowService;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class OgrencisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mailbox_local_part')
                    ->label('E-Posta')
                    ->formatStateUsing(fn ($state) => $state . '@' . config('mailcow.domain'))
                    ->searchable(),
                TextColumn::make('sinif.ad')
                    ->label('Sınıf')
                    ->sortable(),
                TextColumn::make('sifre')
                    ->label('Şifre')
                    ->copyable()
                    ->copyMessage('Şifre kopyalandı')
                    ->formatStateUsing(function ($record) {
                        if (!$record->qr_token) return '—';
                        $data = json_decode($record->qr_token, true);
                        return $data['password'] ?? '—';
                    })
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false),
                TextColumn::make('qr_svg')
                    ->label('QR Kod')
                    ->formatStateUsing(fn ($state) => $state
                        ? '<div style="width:72px;height:72px;overflow:hidden">' . $state . '</div>'
                        : '—')
                    ->html()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false),
                TextColumn::make('created_at')
                    ->label('Kayıt Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('change_password')
                    ->label('Şifre Değiştir')
                    ->icon('heroicon-o-key')
                    ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                    ->form([
                        TextInput::make('new_password')
                            ->label('Yeni Şifre')
                            ->password()
                            ->required()
                            ->minLength(8),
                        TextInput::make('new_password_confirmation')
                            ->label('Şifre Tekrar')
                            ->password()
                            ->required()
                            ->same('new_password'),
                    ])
                    ->action(function (Ogrenci $record, array $data) {
                        try {
                            $mailService = new MailcowService();
                            $email = $record->mailbox_local_part . '@' . config('mailcow.domain');

                            $mailService->updateMailboxPassword($email, $data['new_password']);

                            if ($record->user) {
                                $record->user->password = Hash::make($data['new_password']);
                                $record->user->save();
                            }

                            if ($record->qr_token) {
                                $qrData = json_decode($record->qr_token, true);
                                $qrData['password'] = $data['new_password'];
                                $record->qr_token = json_encode($qrData);
                                $record->save();
                            }

                            Notification::make()
                                ->title('Şifre Başarıyla Güncellendi')
                                ->body("{$record->user->name} için şifre değiştirildi.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Hata')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('view_mailbox')
                    ->label('Mail Durumu')
                    ->icon('heroicon-o-envelope')
                    ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                    ->action(function (Ogrenci $record) {
                        try {
                            $mailService = new MailcowService();
                            $email = $record->mailbox_local_part . '@' . config('mailcow.domain');
                            $info = $mailService->getMailboxInfo($email);

                            Notification::make()
                                ->title('Mailbox Bilgileri')
                                ->body("Kullanılan: " . number_format($info['quota_used'] / 1024, 2) . " MB / " . number_format($info['quota'] / 1024, 2) . " MB (" . $info['percent_used'] . "%)")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Hata')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('change_quota')
                    ->label('Kota Değiştir')
                    ->icon('heroicon-o-chart-pie')
                    ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                    ->form([
                        TextInput::make('quota_mb')
                            ->label('Kota (MB)')
                            ->numeric()
                            ->required()
                            ->minValue(100)
                            ->maxValue(1024)
                            ->default(100)
                            ->helperText('100 – 1024 MB arası'),
                    ])
                    ->action(function (Ogrenci $record, array $data) {
                        try {
                            $mailService = new MailcowService();
                            $email = $record->mailbox_local_part . '@' . config('mailcow.domain');
                            $mailService->updateMailboxQuota($email, (int) $data['quota_mb']);

                            Notification::make()
                                ->title('Kota Güncellendi')
                                ->body("{$record->user->name} için kota {$data['quota_mb']} MB olarak ayarlandı.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Hata')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('generate_qr')
                    ->label('QR Kod Oluştur')
                    ->icon('heroicon-o-qr-code')
                    ->visible(fn ($record) => (auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false) && (empty($record->qr_token) || empty($record->qr_svg)))
                    ->action(function (Ogrenci $record) {
                        $record->loadMissing('user');
                        if (!$record->user) {
                            Notification::make()->title('Hata')->body('Kullanıcı bulunamadı.')->danger()->send();
                            return;
                        }
                        $email = $record->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');
                        $existingToken = $record->qr_token ? json_decode($record->qr_token, true) : null;
                        if ($existingToken && isset($existingToken['password'])) {
                            $password = $existingToken['password'];
                            $qrToken = $existingToken['token'] ?? Str::random(32);
                        } else {
                            $password = Str::random(12);
                            $qrToken = Str::random(32);
                            try {
                                app(MailcowService::class)->updateMailboxPassword($email, $password);
                            } catch (\Exception $e) {
                                Notification::make()->title('Mailcow Hatası')->body($e->getMessage())->warning()->send();
                            }
                        }
                        $qrContent = json_encode(['email' => $email, 'password' => $password, 'token' => $qrToken]);
                        $record->qr_token = $qrContent;
                        $record->qr_svg = (string) QrCode::size(400)->generate($qrContent);
                        $record->save();
                        Notification::make()->title('QR Kod Oluşturuldu')->success()->send();
                    }),
                Action::make('print_badge')
                    ->label('Yaka Kartı')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('ogrenci.yaka-karti', $record))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('generate_qr_bulk')
                        ->label('Toplu QR Kod Oluştur')
                        ->icon('heroicon-o-qr-code')
                        ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false)
                        ->action(function (Collection $records) {
                            $updated = 0;
                            foreach ($records as $record) {
                                if (!empty($record->qr_token) && !empty($record->qr_svg)) continue;
                                $record->loadMissing('user');
                                if (!$record->user) continue;
                                $email = $record->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');
                                $existingToken = $record->qr_token ? json_decode($record->qr_token, true) : null;
                                if ($existingToken && isset($existingToken['password'])) {
                                    $password = $existingToken['password'];
                                    $qrToken = $existingToken['token'] ?? Str::random(32);
                                } else {
                                    $password = Str::random(12);
                                    $qrToken = Str::random(32);
                                    try {
                                        app(MailcowService::class)->updateMailboxPassword($email, $password);
                                    } catch (\Exception $e) {}
                                }
                                $qrContent = json_encode(['email' => $email, 'password' => $password, 'token' => $qrToken]);
                                $record->qr_token = $qrContent;
                                $record->qr_svg = (string) QrCode::size(400)->generate($qrContent);
                                $record->save();
                                $updated++;
                            }
                            Notification::make()->title("{$updated} öğrenci için QR kod oluşturuldu.")->success()->send();
                        }),
                    BulkAction::make('print_badges')
                        ->label('Toplu Yaka Kartı')
                        ->icon('heroicon-o-printer')
                        ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false)
                        ->action(function (Collection $records) {
                            $ids = $records->pluck('id')->implode(',');
                            return redirect()->route('ogrenci.yaka-karti.bulk', ['ids' => $ids]);
                        }),
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'yonetici', 'ogretmen']) ?? false),
                    BulkAction::make('reset_passwords')
                        ->label('Toplu Şifre Sıfırla')
                        ->icon('heroicon-o-key')
                        ->visible(fn () => auth()->user()?->hasRole('admin') ?? false)
                        ->form([
                            TextInput::make('new_password')
                                ->label('Yeni Şifre')
                                ->password()
                                ->required()
                                ->minLength(8),
                            TextInput::make('new_password_confirmation')
                                ->label('Şifre Tekrar')
                                ->password()
                                ->required()
                                ->same('new_password'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $mailService = new MailcowService();
                            $success = 0;
                            $failed = 0;

                            foreach ($records as $record) {
                                try {
                                    $email = $record->mailbox_local_part . '@' . config('mailcow.domain');
                                    $mailService->updateMailboxPassword($email, $data['new_password']);

                                    if ($record->user) {
                                        $record->user->password = Hash::make($data['new_password']);
                                        $record->user->save();
                                    }

                                    if ($record->qr_token) {
                                        $qrData = json_decode($record->qr_token, true);
                                        $qrData['password'] = $data['new_password'];
                                        $record->qr_token = json_encode($qrData);
                                        $record->save();
                                    }

                                    $success++;
                                } catch (\Exception $e) {
                                    $failed++;
                                }
                            }

                            Notification::make()
                                ->title('Toplu Şifre Sıfırlama Tamamlandı')
                                ->body("Başarılı: {$success}, Başarısız: {$failed}")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->headerActions([]);
    }
}
