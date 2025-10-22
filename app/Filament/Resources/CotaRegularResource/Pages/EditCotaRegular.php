<?php

namespace App\Filament\Resources\CotaRegularResource\Pages;

use App\Filament\Resources\CotaRegularResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCotaRegular extends EditRecord
{
    protected static string $resource = CotaRegularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
