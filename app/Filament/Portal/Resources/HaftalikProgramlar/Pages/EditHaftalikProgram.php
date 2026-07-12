<?php

namespace App\Filament\Portal\Resources\HaftalikProgramlar\Pages;

use App\Filament\Portal\Resources\HaftalikProgramlar\HaftalikProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHaftalikProgram extends EditRecord
{
    protected static string $resource = HaftalikProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
