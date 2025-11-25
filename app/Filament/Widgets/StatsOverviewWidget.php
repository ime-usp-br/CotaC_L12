<?php

namespace App\Filament\Widgets;

use App\Models\Consumidor;
use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Consumers
        $totalConsumers = Consumidor::count();

        // Orders
        $ordersToday = Pedido::whereDate('created_at', $today)->count();
        $ordersMonth = Pedido::whereDate('created_at', '>=', $startOfMonth)->count();

        // Revenue (Approximation based on ItemPedido)
        // Ideally we should cache this or have a total column in Pedido
        $revenueToday = Pedido::whereDate('pedidos.created_at', $today)
            ->join('item_pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
            ->sum(\DB::raw('item_pedidos.quantidade * item_pedidos.valor_unitario'));

        $revenueMonth = Pedido::whereDate('pedidos.created_at', '>=', $startOfMonth)
            ->join('item_pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
            ->sum(\DB::raw('item_pedidos.quantidade * item_pedidos.valor_unitario'));

        // Average Ticket (Month)
        $avgTicket = $ordersMonth > 0 ? $revenueMonth / $ordersMonth : 0;

        return [
            Stat::make('Total Consumidores', $totalConsumers)
                ->description('Cadastrados no sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pedidos Hoje', $ordersToday)
                ->description("Mês: {$ordersMonth}")
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success'),

            Stat::make('Cotas Consumidas Hoje', number_format((float) $revenueToday, 0, ',', '.'))
                ->description('Mês: '.number_format((float) $revenueMonth, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color('success'),

            Stat::make('Média de Cotas (Mês)', number_format($avgTicket, 1, ',', '.'))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}
