<?php

namespace App\Filament\Portal\Resources\Odevler;

use App\Filament\Portal\Resources\Odevler\Pages\CreateOdev;
use App\Filament\Portal\Resources\Odevler\Pages\EditOdev;
use App\Filament\Portal\Resources\Odevler\Pages\ListOdevler;
use App\Filament\Portal\Resources\Odevler\Schemas\OdevForm;
use App\Filament\Portal\Resources\Odevler\Tables\OdevlerTable;
use App\Models\Odev;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class OdevResource extends Resource
{
    protected static ?string $model = Odev::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationLabel(): string
    {
        return 'Ödevler';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Öğrenci İşlemleri';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $roles = $user->roles()->pluck('name')->toArray();
        return in_array('admin', $roles) || in_array('ogretmen', $roles) || in_array('yonetici', $roles);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $roles = $user->roles()->pluck('name')->toArray();
        return in_array('admin', $roles) || in_array('ogretmen', $roles);
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $roles = $user->roles()->pluck('name')->toArray();
        return in_array('admin', $roles) || in_array('ogretmen', $roles);
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        $roles = $user->roles()->pluck('name')->toArray();
        return in_array('admin', $roles) || in_array('ogretmen', $roles);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->hasRole('ogretmen')) {
            return $query->where('ogretmen_id', $user->id);
        }

        if ($user?->hasRole('yonetici')) {
            return $query->whereHas('sinif.okul', fn($q) => $q->where('yonetici_user_id', $user->id)
                ->when($user->okul_id, fn($q) => $q->orWhere('id', $user->okul_id)));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return OdevForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OdevlerTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOdevler::route('/'),
            'create' => CreateOdev::route('/create'),
            'edit' => EditOdev::route('/{record}/edit'),
        ];
    }
}
