<?php

namespace App\Filament\Portal\Resources\Ogrencis;

use App\Filament\Portal\Resources\Ogrencis\Pages\CreateOgrenci;
use App\Filament\Portal\Resources\Ogrencis\Pages\EditOgrenci;
use App\Filament\Portal\Resources\Ogrencis\Pages\ListOgrencis;
use App\Filament\Portal\Resources\Ogrencis\Schemas\OgrenciForm;
use App\Filament\Portal\Resources\Ogrencis\Tables\OgrencisTable;
use App\Models\Ogrenci;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class OgrenciResource extends Resource
{
    protected static ?string $model = Ogrenci::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    public static function getNavigationLabel(): string
    {
        return 'Öğrenciler';
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
        return in_array('admin', $roles) || in_array('yonetici', $roles) || in_array('ogretmen', $roles);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $roles = $user->roles()->pluck('name')->toArray();
        return in_array('admin', $roles) || in_array('yonetici', $roles) || in_array('ogretmen', $roles);
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        
        $roles = $user->roles()->pluck('name')->toArray();
        return in_array('admin', $roles) || in_array('yonetici', $roles) || in_array('ogretmen', $roles);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->hasRole('yonetici')) {
            return $query->whereHas('sinif.okul', fn($q) => $q->where('yonetici_user_id', $user->id));
        }

        if ($user?->hasRole('ogretmen')) {
            return $query->whereHas('sinif', fn($q) => $q
                ->where('ogretmen_user_id', $user->id)
                ->orWhereHas('ogretmenler', fn($q2) => $q2->where('users.id', $user->id))
            );
        }

        if ($user?->hasRole('veli')) {
            return $query->whereHas('veliler', fn($q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return OgrenciForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OgrencisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOgrencis::route('/'),
            'create' => CreateOgrenci::route('/create'),
            'edit' => EditOgrenci::route('/{record}/edit'),
        ];
    }
}
