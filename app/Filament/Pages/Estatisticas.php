<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ConsumptionByCategoryWidget;
use App\Filament\Widgets\OrdersChartWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TopProductsWidget;
use BackedEnum;
use Filament\Pages\Page;

class Estatisticas extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Estatísticas';

    protected static ?string $title = 'Estatísticas';

    protected static ?string $slug = 'estatisticas';

    protected string $view = 'filament.pages.estatisticas';

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('voltar')
                ->label('Voltar')
                ->icon('heroicon-o-arrow-left')
                ->url(\App\Filament\Pages\Dashboard::getUrl())
                ->color('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            OrdersChartWidget::class,
            ConsumptionByCategoryWidget::class,
            TopProductsWidget::class,
            RecentActivityWidget::class,
        ];
    }
}
