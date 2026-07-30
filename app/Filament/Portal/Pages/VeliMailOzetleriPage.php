<?php

namespace App\Filament\Portal\Pages;

use App\Models\MailAktiviteLog;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VeliMailOzetleriPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'mail-ozetleri';

    protected string $view = 'filament.portal.pages.veli-mail-ozetleri';

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
            return MailAktiviteLog::whereRaw('1 = 0');
        }

        $ogrenciIds = $veli->ogrenciler()->pluck('ogrenciler.id');

        return MailAktiviteLog::whereIn('ogrenci_id', $ogrenciIds)
            ->with('ogrenci.user', 'ogrenci.sinif');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('ogrenci.user.name')
                    ->label('Öğrenci')
                    ->sortable(),
                TextColumn::make('tip')
                    ->label('Yön')
                    ->formatStateUsing(fn ($state) => $state === 'gonderilen' ? 'Giden' : 'Gelen')
                    ->badge()
                    ->color(fn ($state) => $state === 'gonderilen' ? 'primary' : 'success'),
                TextColumn::make('karsikisi')
                    ->label('Kişi')
                    ->getStateUsing(function (MailAktiviteLog $record) {
                        if ($record->tip === 'gonderilen') {
                            return $record->kime ?: '-';
                        }
                        return $record->kimden ?: '-';
                    }),
                TextColumn::make('konu')
                    ->label('Özet')
                    ->formatStateUsing(fn ($state) => $state
                        ? (mb_strlen($state) > 140 ? mb_substr($state, 0, 140) . '...' : $state)
                        : '(Konusuz)')
                    ->wrap(),
                TextColumn::make('tarih')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('tarih', 'desc')
            ->paginated([20, 50, 100])
            ->striped();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('veli') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Mail Özetleri';
    }
}
