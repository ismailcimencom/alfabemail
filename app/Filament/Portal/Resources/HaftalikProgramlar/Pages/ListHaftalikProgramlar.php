<?php

namespace App\Filament\Portal\Resources\HaftalikProgramlar\Pages;

use App\Filament\Portal\Resources\HaftalikProgramlar\HaftalikProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHaftalikProgramlar extends ListRecords
{
    protected static string $resource = HaftalikProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Yeni Program Ekle'),
        ];
    }
}
