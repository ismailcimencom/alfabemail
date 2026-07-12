<?php

namespace App\Filament\Portal\Resources\HaftalikProgramlar;

use App\Filament\Portal\Resources\HaftalikProgramlar\Pages\CreateHaftalikProgram;
use App\Filament\Portal\Resources\HaftalikProgramlar\Pages\EditHaftalikProgram;
use App\Filament\Portal\Resources\HaftalikProgramlar\Pages\ListHaftalikProgramlar;
use App\Filament\Portal\Resources\HaftalikProgramlar\Schemas\HaftalikProgramForm;
use App\Filament\Portal\Resources\HaftalikProgramlar\Tables\HaftalikProgramlarTable;
use App\Models\HaftalikProgram;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class HaftalikProgramResource extends Resource
{
    protected static ?string $model = HaftalikProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $label = 'Haftalık Program';

    protected static ?string $pluralLabel = 'Haftalık Program';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        if (!SchemaFacade::hasTable('haftalik_programlar')) return false;
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAnyRole(['admin', 'yonetici']);
    }

    public static function canAccess(): bool
    {
        if (!SchemaFacade::hasTable('haftalik_programlar')) return false;
        return auth()->user()?->hasAnyRole(['admin', 'yonetici']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'yonetici']) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'yonetici']) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'yonetici']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return HaftalikProgramForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HaftalikProgramlarTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->hasRole('yonetici')) {
            return $query->whereHas('okul', fn($q) => $q->where('yonetici_user_id', $user->id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHaftalikProgramlar::route('/'),
            'create' => CreateHaftalikProgram::route('/create'),
            'edit' => EditHaftalikProgram::route('/{record}/edit'),
        ];
    }
}
