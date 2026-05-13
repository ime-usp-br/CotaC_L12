<?php

namespace Tests\Unit\Models;

use App\Models\Consumidor;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Tests\TestCase;

class PedidoTest extends TestCase
{
    /**
     * Testa que o modelo tem a relação consumidor.
     */
    public function test_tem_relacao_consumidor(): void
    {
        $consumidor = Consumidor::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        $this->assertInstanceOf(Consumidor::class, $pedido->consumidor);
        $this->assertEquals($consumidor->codpes, $pedido->consumidor->codpes);
    }

    /**
     * Testa que o modelo tem a relação itens.
     */
    public function test_tem_relacao_itens(): void
    {
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'valor_unitario' => $produto->valor,
        ]);

        $this->assertCount(1, $pedido->itens);
        $this->assertInstanceOf(ItemPedido::class, $pedido->itens->first());
    }

    /**
     * Testa que o modelo tem a relação produtos via pivot.
     */
    public function test_tem_relacao_produtos_via_pivot(): void
    {
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'valor_unitario' => $produto->valor,
        ]);

        $this->assertCount(1, $pedido->produtos);
        $this->assertInstanceOf(Produto::class, $pedido->produtos->first());
    }

    /**
     * Testa que fillable contém os campos esperados.
     */
    public function test_fillable_contem_codpes_e_estado(): void
    {
        $pedido = new Pedido;

        $this->assertEquals(['consumidor_codpes', 'estado'], $pedido->getFillable());
    }
}
