<?php

namespace App\Filament\Resources\ConsumidorResource\Pages;

use App\Filament\Resources\ConsumidorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditConsumidor extends EditRecord
{
    protected static string $resource = ConsumidorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
