<?php

namespace Tests\Feature\Livewire;

use App\Livewire\BuscaConsumidor;
use App\Livewire\CarrinhoPedido;
use App\Livewire\ListaPedidosPendentes;
use App\Models\Consumidor;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ToastDispatchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function busca_consumidor_dispatches_error_toast_for_invalid_nusp(): void
    {
        Livewire::test(BuscaConsumidor::class)
            ->set('codpes', 'abc')
            ->call('buscar')
            ->assertHasErrors(['codpes']);
    }

    #[Test]
    public function carrinho_pedido_dispatches_error_toast_when_cart_is_empty(): void
    {
        Livewire::test(CarrinhoPedido::class)
            ->call('finalizarPedido')
            ->assertDispatched('toast', type: 'error');
    }

    #[Test]
    public function carrinho_pedido_dispatches_error_toast_when_saldo_insufficient(): void
    {
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create(['valor' => 1000]);

        Livewire::test(CarrinhoPedido::class)
            ->set('codpes', $consumidor->codpes)
            ->set('saldoDisponivel', 500)
            ->set('itens', [$produto->id => 1])
            ->call('finalizarPedido')
            ->assertDispatched('toast', type: 'error');
    }

    #[Test]
    public function lista_pedidos_dispatches_success_toast_when_marking_as_delivered(): void
    {
        $pedido = Pedido::factory()->create(['estado' => 'REALIZADO']);

        Livewire::test(ListaPedidosPendentes::class)
            ->call('marcarComoEntregue', $pedido->id)
            ->assertDispatched('toast', type: 'success');

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'ENTREGUE',
        ]);
    }

    #[Test]
    public function toast_events_are_dispatched_with_correct_structure(): void
    {
        Livewire::test(CarrinhoPedido::class)
            ->call('finalizarPedido')
            ->assertDispatched('toast');
    }

    #[Test]
    public function toast_type_is_error_for_empty_cart(): void
    {
        Livewire::test(CarrinhoPedido::class)
            ->call('finalizarPedido')
            ->assertDispatched('toast', type: 'error');
    }
}
