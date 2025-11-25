<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtratoResource\Pages;
use App\Models\ItemPedido;
use App\Models\Pedido;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExtratoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'Extrato';

    protected static ?string $pluralModelLabel = 'Extratos';

    protected static ?string $slug = 'extratos';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()?->can('ver_extratos');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['consumidor', 'itens.produto']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Data/Hora')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('consumidor.nome')
                    ->label('Consumidor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('consumidor.codpes')
                    ->label('N° USP')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('produtos_resumo')
                    ->label('Produtos')
                    ->state(function (Pedido $record) {
                        return $record->itens->map(fn (ItemPedido $item) => "{$item->quantidade}x {$item->produto->nome}")->join(', ');
                    })
                    ->wrap(),
                TextColumn::make('valor_total_calculado')
                    ->label('Total de Cotas')
                    ->state(function (Pedido $record) {
                        // Assuming logic to calculate total, or if it's stored.
                        // Based on rules: sum(qty * price)
                        // We might need to load items.produto
                        return number_format($record->itens->sum(fn (ItemPedido $i) => $i->quantidade * $i->valor_unitario), 0, ',', '.').' Cotas';
                    }),
                TextColumn::make('estado')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'warning' => 'REALIZADO',
                        'success' => 'ENTREGUE',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('periodo')
                    ->form([
                        DatePicker::make('data_inicio')->label('Data Início'),
                        DatePicker::make('data_fim')->label('Data Fim'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['data_inicio'],
                                fn (Builder $query, $date) => $query->whereDate('created_at', '>=', is_string($date) ? $date : ''),
                            )
                            ->when(
                                $data['data_fim'],
                                fn (Builder $query, $date) => $query->whereDate('created_at', '<=', is_string($date) ? $date : ''),
                            );
                    }),
                SelectFilter::make('estado')
                    ->label('Status')
                    ->options([
                        'REALIZADO' => 'REALIZADO',
                        'ENTREGUE' => 'ENTREGUE',
                    ]),
            ])
            ->recordActions([
                // No actions
            ])
            ->toolbarActions([
                // Export action will be added in List page or here
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtratos::route('/'),
        ];
    }
}
