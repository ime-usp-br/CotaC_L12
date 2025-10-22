<?php

namespace App\Filament\Resources\CotaRegularResource\Pages;

use App\Filament\Resources\CotaRegularResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCotaRegulares extends ListRecords
{
    protected static string $resource = CotaRegularResource::class;

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
