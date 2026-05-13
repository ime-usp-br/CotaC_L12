<?php

namespace Tests\Unit\Models;

use App\Models\Consumidor;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Tests\TestCase;

class ItemPedidoTest extends TestCase
{
    /**
     * Testa que o modelo tem a relação pedido.
     */
    public function test_tem_relacao_pedido(): void
    {
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        $item = ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'valor_unitario' => $produto->valor,
        ]);

        $this->assertInstanceOf(Pedido::class, $item->pedido);
        $this->assertEquals($pedido->id, $item->pedido->id);
    }

    /**
     * Testa que o modelo tem a relação produto.
     */
    public function test_tem_relacao_produto(): void
    {
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        $item = ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'valor_unitario' => $produto->valor,
        ]);

        $this->assertInstanceOf(Produto::class, $item->produto);
        $this->assertEquals($produto->id, $item->produto->id);
    }

    /**
     * Testa que fillable contém todos os campos esperados.
     */
    public function test_fillable_contem_todos_campos(): void
    {
        $item = new ItemPedido;

        $this->assertEquals(
            ['pedido_id', 'produto_id', 'quantidade', 'valor_unitario'],
            $item->getFillable()
        );
    }
}
