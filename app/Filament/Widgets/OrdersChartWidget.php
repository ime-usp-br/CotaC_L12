<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrdersChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pedidos por Dia (Últimos 30 dias)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        $start = Carbon::now()->subDays(29);
        $end = Carbon::now();

        // Initialize all days with 0
        $current = $start->copy();
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d/m');
            $data[$dateStr] = 0;
            $current->addDay();
        }

        // Fetch data
        $results = Pedido::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereDate('created_at', '>=', $start)
            ->groupBy('date')
            ->get();

        foreach ($results as $result) {
            /** @var \stdClass $result */
            $data[$result->date] = $result->count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pedidos',
                    'data' => array_values($data),
                    'fill' => 'start',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
