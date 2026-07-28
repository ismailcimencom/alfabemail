<?php

namespace App\Filament\Portal\Resources\Ogretmenler;

use App\Filament\Portal\Resources\Ogretmenler\Pages\CreateOgretmen;
use App\Filament\Portal\Resources\Ogretmenler\Pages\EditOgretmen;
use App\Filament\Portal\Resources\Ogretmenler\Schemas\OgretmenForm;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OgretmenlerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'ogretmenler';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $label = 'Öğretmen';

    protected static ?string $pluralLabel = 'Öğretmenler';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAnyRole(['admin', 'ogretmen']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'ogretmen']) ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->role('ogretmen');
        $user = auth()->user();

        if ($user?->hasRole('ogretmen')) {
            $sinifIds = \App\Models\Sinif::whereHas('ogretmenler', fn($q) => $q->where('users.id', $user->id))
                ->pluck('id');

            $query->whereHas('ogretmen_sinifler_pivot', fn($q) => $q->whereIn('sinif_id', $sinifIds));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return OgretmenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Ad Soyad')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label('Eklenme Tarihi')
                    ->dateTime('d.m.Y'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->paginated([10, 25, 50]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Portal\Resources\Ogretmenler\Pages\ListOgretmenler::route('/'),
            'create' => CreateOgretmen::route('/create'),
            'edit' => EditOgretmen::route('/{record}/edit'),
        ];
    }
}