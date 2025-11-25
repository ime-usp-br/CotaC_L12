<?php

namespace App\Filament\Widgets;

use App\Models\Produto;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Produtos Mais Vendidos';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        /** @var \Illuminate\Support\Collection<int, \App\Models\Produto> */
        $cachedData = Cache::remember('dashboard.top_products', now()->addMinutes(30), function () {
            return Produto::query()
                ->join('item_pedidos', 'produtos.id', '=', 'item_pedidos.produto_id')
                ->select('produtos.id', 'produtos.nome', DB::raw('SUM(item_pedidos.quantidade) as total_vendido'))
                ->groupBy('produtos.id', 'produtos.nome')
                ->orderByDesc('total_vendido')
                ->limit(5)
                ->get();
        });

        return $table
            ->query(
                Produto::query()->whereIn('id', $cachedData->pluck('id'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Produto')
                    ->limit(20),
                Tables\Columns\TextColumn::make('total_vendido')
                    ->label('Qtd.')
                    ->state(function (Produto $record) use ($cachedData) {
                        $item = $cachedData->firstWhere('id', $record->id);

                        return $item && property_exists($item, 'total_vendido') ? $item->total_vendido : 0;
                    })
                    ->alignRight(),
            ])
            ->paginated(false);
    }
}
