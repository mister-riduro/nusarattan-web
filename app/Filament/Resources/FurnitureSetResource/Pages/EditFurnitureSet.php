<?php

namespace App\Filament\Resources\FurnitureSetResource\Pages;

use App\Filament\Resources\FurnitureSetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFurnitureSet extends EditRecord
{
    protected static string $resource = FurnitureSetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
