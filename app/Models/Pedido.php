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
}
