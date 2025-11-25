<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConsumptionByCategoryWidget extends ChartWidget
{
    protected ?string $heading = 'Consumo por Categoria';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        /** @var array<string, mixed> */
        return Cache::remember('dashboard.consumption_by_category', now()->addMinutes(15), function () {
            $data = Pedido::query()
                ->join('consumidores', 'pedidos.consumidor_codpes', '=', 'consumidores.codpes')
                ->join('item_pedidos', 'pedidos.id', '=', 'item_pedidos.pedido_id')
                ->select('consumidores.categoria', DB::raw('SUM(item_pedidos.quantidade * item_pedidos.valor_unitario) as total_gasto'))
                ->whereNotNull('consumidores.categoria')
                ->groupBy('consumidores.categoria')
                ->get();

            $labels = $data->pluck('categoria')->toArray();
            $values = $data->pluck('total_gasto')->toArray();

            return [
                'datasets' => [
                    [
                        'label' => 'Consumo (Cotas)',
                        'data' => $values,
                        'backgroundColor' => [
                            'rgb(54, 162, 235)',   // Blue
                            'rgb(255, 99, 132)',   // Red
                            'rgb(255, 205, 86)',   // Yellow
                            'rgb(75, 192, 192)',   // Green
                            'rgb(153, 102, 255)',  // Purple
                        ],
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
