<?php

namespace App\Filament\Widgets;

use App\Models\Produto;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Produtos Mais Vendidos';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Produto::query()
                    ->join('item_pedidos', 'produtos.id', '=', 'item_pedidos.produto_id')
                    ->select('produtos.id', 'produtos.nome', DB::raw('SUM(item_pedidos.quantidade) as total_vendido'))
                    ->groupBy('produtos.id', 'produtos.nome')
                    ->orderByDesc('total_vendido')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->label('Produto')
                    ->limit(20),
                Tables\Columns\TextColumn::make('total_vendido')
                    ->label('Qtd.')
                    ->alignRight(),
            ])
            ->paginated(false);
    }
}
