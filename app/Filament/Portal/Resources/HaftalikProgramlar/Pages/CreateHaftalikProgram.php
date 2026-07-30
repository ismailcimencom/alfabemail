<?php

namespace App\Filament\Portal\Resources\HaftalikProgramlar\Pages;

use App\Filament\Portal\Resources\HaftalikProgramlar\HaftalikProgramResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateHaftalikProgram extends CreateRecord
{
    protected static string $resource = HaftalikProgramResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Oluştur')
                ->submit('create'),
            Action::make('cancel')
                ->label('İptal')
                ->url($this->previousUrl ?? static::getUrl())
                ->color('gray'),
        ];
    }
}
