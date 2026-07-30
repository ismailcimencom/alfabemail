<?php

namespace App\Filament\Portal\Pages;

use App\Models\VeliMesaj;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class VeliMesajPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'mesajlar';

    protected string $view = 'filament.portal.pages.veli-mesaj';

    public array $students = [];

    public ?string $konu = null;

    public ?string $kime = null;

    public ?string $mesaj = null;

    public function mount(): void
    {
        $user = Auth::user();
        $veli = $user->veli;

        if (!$veli) {
            return;
        }

        $this->students = $veli->ogrenciler()
            ->with('user', 'sinif.ogretmenler')
            ->get()
            ->toArray();
    }

    public function content(Schema $schema): Schema
    {
        $teacherOptions = [];
        foreach ($this->students as $student) {
            if (isset($student['sinif']) && $student['sinif']) {
                foreach ($student['sinif']['ogretmenler'] ?? [] as $ogretmen) {
                    $teacherOptions[$ogretmen['id']] = $ogretmen['name'] . ' (' . $student['user']['name'] . ')';
                }
            }
        }

        return $schema
            ->components([
                Section::make('Öğretmene Mesaj')
                    ->columns(1)
                    ->schema([
                        TextInput::make('konu')
                            ->label('Konu')
                            ->placeholder('Mesaj konusu')
                            ->required(),
                        Select::make('kime')
                            ->label('Alıcı Öğretmen')
                            ->options($teacherOptions)
                            ->placeholder('Öğretmen seçin')
                            ->required(),
                        Textarea::make('mesaj')
                            ->label('Mesaj')
                            ->placeholder('Mesajınızı yazın...')
                            ->required()
                            ->rows(4),
                    ])
                    ->footerActions([
                        Action::make('submit')
                            ->label('Mesajı Gönder')
                            ->action('submit'),
                    ]),
            ]);
    }

    public function submit(): void
    {
        $data = $this->validate([
            'konu' => 'required|string|max:255',
            'kime' => 'required',
            'mesaj' => 'required|string',
        ]);

        try {
            $veli = Auth::user()->veli;
            $ogrenciId = count($this->students) > 0 ? $this->students[0]['id'] : null;

            VeliMesaj::create([
                'veli_id' => $veli->id,
                'ogretmen_user_id' => $data['kime'],
                'ogrenci_id' => $ogrenciId,
                'konu' => $data['konu'],
                'mesaj' => $data['mesaj'],
            ]);

            Notification::make()
                ->title('Mesaj Gönderildi')
                ->body('Mesajınız başarıyla gönderildi.')
                ->success()
                ->send();

            $this->konu = null;
            $this->kime = null;
            $this->mesaj = null;
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata')
                ->body('Mesaj gönderilemedi: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('veli') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Mesajlar';
    }
}
