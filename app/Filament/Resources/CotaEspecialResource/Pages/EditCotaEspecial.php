<?php

namespace App\Filament\Resources\CotaEspecialResource\Pages;

use App\Filament\Resources\CotaEspecialResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCotaEspecial extends EditRecord
{
    protected static string $resource = CotaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
