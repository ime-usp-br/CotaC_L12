<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pedido - Representa um pedido realizado por um consumidor.
 *
 * Cada pedido contém múltiplos itens (produtos com quantidades)
 * e possui um estado (REALIZADO, ENTREGUE).
 */
class Pedido extends Model
{
    /** @use HasFactory<\Database\Factories\PedidoFactory> */
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'consumidor_codpes',
        'estado',
    ];

    /**
     * Retorna o consumidor que realizou o pedido.
     *
     * @return BelongsTo<Consumidor, $this>
     */
    public function consumidor(): BelongsTo
    {
        return $this->belongsTo(Consumidor::class, 'consumidor_codpes', 'codpes');
    }

    /**
     * Retorna a coleção de ItemPedido associados ao pedido.
     *
     * @return HasMany<ItemPedido, $this>
     */
    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    /**
     * Retorna a coleção de Produto através dos itens do pedido.
     *
     * @return BelongsToMany<Produto, $this>
     */
    public function produtos(): BelongsToMany
    {
        return $this->belongsToMany(Produto::class, 'item_pedidos')
            ->withPivot('quantidade')
            ->withTimestamps();
    }

    /**
     * Scope para pedidos realizados (pendentes de entrega).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Pedido>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Pedido>
     */
    public function scopeRealizados($query)
    {
        return $query->where('estado', 'REALIZADO');
    }

    /**
     * Scope para pedidos do mês atual.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Pedido>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Pedido>
     */
    public function scopeDoMesAtual($query)
    {
        return $query->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
    }

    /**
     * Scope para pedidos de um mês/ano específico.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Pedido>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Pedido>
     */
    public function scopeDoMes($query, int $ano, int $mes)
    {
        return $query->whereYear('created_at', $ano)
            ->whereMonth('created_at', $mes);
    }

    /**
     * Scope para pedidos de um consumidor.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Pedido>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Pedido>
     */
    public function scopePorConsumidor($query, int $codpes)
    {
        return $query->where('consumidor_codpes', $codpes);
    }

    /**
     * Scope com eager loading de relacionamentos comuns.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<Pedido>  $query
     * @return \Illuminate\Database\Eloquent\Builder<Pedido>
     */
    public function scopeComRelacionamentos($query)
    {
        return $query->with(['consumidor', 'itens.produto']);
    }
}
