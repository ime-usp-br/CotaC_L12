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
            'estado' => Pedido::ESTADO_REALIZADO,
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
            'estado' => Pedido::ESTADO_REALIZADO,
        ]);

        Livewire::test(ListaPedidosPendentes::class)
            ->call('marcarComoEntregue', $pedido->id);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => Pedido::ESTADO_ENTREGUE,
        ]);
    }

    public function test_does_not_show_delivered_orders()
    {
        $consumidor = Consumidor::factory()->create();
        $pedido = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => Pedido::ESTADO_ENTREGUE,
        ]);

        Livewire::test(ListaPedidosPendentes::class)
            ->assertDontSee('Pedido #'.$pedido->id);
    }

    public function test_orders_are_sorted_newest_first()
    {
        $consumidor = Consumidor::factory()->create();
        $older = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => Pedido::ESTADO_REALIZADO,
            'created_at' => now()->subMinutes(30),
        ]);
        $newer = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => Pedido::ESTADO_REALIZADO,
            'created_at' => now()->subMinutes(5),
        ]);

        $component = Livewire::test(ListaPedidosPendentes::class);
        $pedidos = $component->viewData('pedidos');

        $this->assertTrue($pedidos->first()->is($newer));
        $this->assertTrue($pedidos->last()->is($older));
    }

    public function test_does_not_show_orders_older_than_one_hour()
    {
        $consumidor = Consumidor::factory()->create();
        $old = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => Pedido::ESTADO_REALIZADO,
            'created_at' => now()->subMinutes(61),
        ]);

        Livewire::test(ListaPedidosPendentes::class)
            ->assertDontSee('Pedido #'.$old->id);
    }

    public function test_expired_orders_are_not_listed()
    {
        $consumidor = Consumidor::factory()->create();
        $expirado = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => Pedido::ESTADO_EXPIRADO,
            'created_at' => now()->subMinutes(30),
        ]);

        Livewire::test(ListaPedidosPendentes::class)
            ->assertDontSee('Pedido #'.$expirado->id);
    }
}
