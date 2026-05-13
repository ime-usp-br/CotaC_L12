<?php

namespace Tests\Unit\Models;

use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use OwenIt\Auditing\Contracts\Auditable;
use Tests\TestCase;

class ProdutoTest extends TestCase
{
    /**
     * Testa que o modelo tem a relação itemPedidos.
     */
    public function test_tem_relacao_item_pedidos(): void
    {
        $produto = Produto::factory()->create();
        $consumidor = \App\Models\Consumidor::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'valor_unitario' => $produto->valor,
        ]);

        $this->assertCount(1, $produto->itemPedidos);
        $this->assertInstanceOf(ItemPedido::class, $produto->itemPedidos->first());
    }

    /**
     * Testa que fillable contém os campos esperados.
     */
    public function test_fillable_contem_nome_e_valor(): void
    {
        $produto = new Produto;

        $this->assertEquals(['nome', 'valor'], $produto->getFillable());
    }

    /**
     * Testa que o modelo implementa Auditable.
     */
    public function test_implements_auditable(): void
    {
        $produto = new Produto;

        $this->assertInstanceOf(Auditable::class, $produto);
    }
}
