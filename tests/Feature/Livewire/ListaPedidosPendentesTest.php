<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ListaPedidosPendentes;
use App\Models\Consumidor;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListaPedidosPendentesTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_correctly()
    {
        Livewire::test(ListaPedidosPendentes::class)
            ->assertStatus(200);
    }

    public function test_lists_pending_orders()
    {
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create();

        $pedido = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        $pedido->itens()->create([
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'valor_unitario' => $produto->valor,
        ]);

        Livewire::test(ListaPedidosPendentes::class)
            ->assertSee($pedido->id)
            ->assertSee($consumidor->nome)
            ->assertSee($produto->nome);
    }

    public function test_can_mark_order_as_delivered()
    {
        $consumidor = Consumidor::factory()->create();
        $pedido = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        Livewire::test(ListaPedidosPendentes::class)
            ->call('marcarComoEntregue', $pedido->id);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'ENTREGUE',
        ]);
    }

    public function test_does_not_show_delivered_orders()
    {
        $consumidor = Consumidor::factory()->create();
        $pedido = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'ENTREGUE',
        ]);

        Livewire::test(ListaPedidosPendentes::class)
            ->assertDontSee('Pedido #'.$pedido->id);
    }
}
