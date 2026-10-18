<?php

namespace App\Filament\Resources\FurnitureSetResource\Pages;

use App\Filament\Resources\FurnitureSetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFurnitureSets extends ListRecords
{
    protected static string $resource = FurnitureSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
