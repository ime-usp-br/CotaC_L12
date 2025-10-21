<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Produto - Representa os produtos disponíveis para consumo.
 *
 * Cada produto tem um nome único e um valor em unidades de cota.
 *
 * @use HasFactory<\Database\Factories\ProdutoFactory>
 */
class Produto extends Model
{
    /** @use HasFactory<\Database\Factories\ProdutoFactory> */
    use HasFactory;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nome',
        'valor',
    ];

    /**
     * Retorna todas as instâncias de ItemPedido associadas a este produto.
     *
     * @return HasMany<ItemPedido, $this>
     */
    public function itemPedidos(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }
}
