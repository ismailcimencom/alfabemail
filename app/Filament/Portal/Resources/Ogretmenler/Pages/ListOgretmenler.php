<?php

namespace App\Filament\Portal\Resources\Ogretmenler\Pages;

use App\Filament\Portal\Resources\Ogretmenler\OgretmenlerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOgretmenler extends ListRecords
{
    protected static string $resource = OgretmenlerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Öğretmen Davet Et'),
        ];
    }
}