<?php

namespace App\Filament\Portal\Pages;

use App\Models\ActivityLog;
use App\Models\Ogrenci;
use App\Services\MailcowService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VeliOgrencilerPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'ogrenciler';

    protected string $view = 'filament.portal.pages.veli-ogrenciler';

    public array $allActivities = [];

    public function mount(): void
    {
        $veli = Auth::user()->veli;
        if (!$veli) {
            return;
        }

        $students = $veli->ogrenciler()->with('user')->get();
        $allActivities = collect();

        foreach ($students as $student) {
            $logs = ActivityLog::where('user_id', $student->user_id)
                ->where('created_at', '>=', now()->subDays(14))
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->toArray();

            foreach ($logs as $log) {
                $log['student_name'] = $student->user->name;
                $allActivities->push($log);
            }
        }

        $this->allActivities = $allActivities->sortByDesc('created_at')->take(20)->values()->toArray();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    public function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $veli = Auth::user()->veli;

        if (!$veli) {
            return Ogrenci::whereRaw('1 = 0');
        }

        $studentIds = $veli->ogrenciler()->pluck('ogrenciler.id');

        return Ogrenci::whereIn('id', $studentIds)->with('user', 'sinif');
    }

    public function table(Table $table): Table
    {
        $mailcow = app(MailcowService::class);

        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('user.name')
                    ->label('Ad Soyad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sinif.ad')
                    ->label('Sınıf'),
                TextColumn::make('user.email')
                    ->label('E-Posta')
                    ->searchable(),
                TextColumn::make('quota')
                    ->label('Kota')
                    ->formatStateUsing(function (Ogrenci $record) use ($mailcow) {
                        $percent = $this->getQuotaPercent($record, $mailcow);
                        $color = $percent > 80 ? 'danger' : ($percent > 50 ? 'warning' : 'success');
                        $bar = '<div class="flex items-center gap-2"><div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full rounded-full bg-' . $color . '-500" style="width: ' . min($percent, 100) . '%"></div></div><span class="text-xs font-medium text-gray-600">%' . $percent . '</span></div>';
                        return $bar;
                    })
                    ->html(),
                TextColumn::make('durum')
                    ->label('Durum')
                    ->formatStateUsing(function (Ogrenci $record) use ($mailcow) {
                        return $this->getQuotaPercent($record, $mailcow) < 80 ? 'Normal' : 'Kota Dolu';
                    })
                    ->badge()
                    ->color(fn (Ogrenci $record) => $this->getQuotaPercent($record, app(MailcowService::class)) < 80 ? 'success' : 'danger'),
            ])
            ->actions([
                Action::make('reset_password')
                    ->label('Şifre Sıfırla')
                    ->icon('heroicon-o-key')
                    ->modalHeading('Şifre Sıfırla')
                    ->modalDescription(fn (Ogrenci $record) => $record->user?->name . ' için yeni şifre belirleyin.')
                    ->form([
                        TextInput::make('yeni_sifre')
                            ->label('Yeni Şifre')
                            ->password()
                            ->required()
                            ->minLength(6),
                        TextInput::make('yeni_sifre_tekrar')
                            ->label('Yeni Şifre (Tekrar)')
                            ->password()
                            ->required()
                            ->same('yeni_sifre'),
                        TextInput::make('veli_sifre')
                            ->label('Kendi Şifreniz (Onay)')
                            ->password()
                            ->required(),
                    ])
                    ->action(function (Ogrenci $record, array $data) {
                        $user = auth()->user();

                        if (!Hash::check($data['veli_sifre'], $user->password)) {
                            Notification::make()
                                ->title('Hata')
                                ->body('Kendi şifrenizi yanlış girdiniz.')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            $email = $record->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');
                            $mailService = app(MailcowService::class);
                            $mailService->updateMailboxPassword($email, $data['yeni_sifre']);

                            if ($record->user) {
                                $record->user->password = Hash::make($data['yeni_sifre']);
                                $record->user->save();
                            }

                            Notification::make()
                                ->title('Şifre Sıfırlandı')
                                ->body($record->user?->name . ' için şifre başarıyla sıfırlandı.')
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
            ])
            ->searchable()
            ->paginated(false);
    }

    private function getQuotaPercent(Ogrenci $student, MailcowService $mailcow): int
    {
        if (!$student->mailbox_local_part) {
            return 0;
        }

        try {
            $email = $student->mailbox_local_part . '@' . config('mailcow.domain', 'alfabe.co');
            $quota = $mailcow->getMailboxQuota($email);
            return $quota['percent_used'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('veli') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Çocuklarım';
    }
}
