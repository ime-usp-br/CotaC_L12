<?php

namespace App\Filament\Resources\CotaEspecialResource\Pages;

use App\Filament\Resources\CotaEspecialResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCotaEspeciais extends ListRecords
{
    protected static string $resource = CotaEspecialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('backToDashboard')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->url(url('/admin'))
                ->color('gray'),
            CreateAction::make(),
        ];
    }
}
