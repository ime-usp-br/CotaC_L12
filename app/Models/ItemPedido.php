<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ItemPedido - Representa um item (produto + quantidade) dentro de um pedido.
 *
 * Esta é a tabela pivot entre Pedido e Produto, armazenando a quantidade
 * de cada produto em um pedido específico.
 *
 * @property int $id
 * @property int $pedido_id
 * @property int $produto_id
 * @property int $quantidade
 * @property int $valor_unitario
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Pedido $pedido
 * @property-read \App\Models\Produto $produto
 */
class ItemPedido extends Model
{
    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pedido_id',
        'produto_id',
        'quantidade',
        'valor_unitario',
    ];

    /**
     * Retorna o pedido ao qual este item pertence.
     *
     * @return BelongsTo<Pedido, $this>
     */
    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Retorna o produto associado a este item.
     *
     * @return BelongsTo<Produto, $this>
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
