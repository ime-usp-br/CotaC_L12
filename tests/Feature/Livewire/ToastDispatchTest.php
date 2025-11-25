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
use Tests\TestCase;

class ToastDispatchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function busca_consumidor_dispatches_error_toast_for_invalid_nusp(): void
    {
        Livewire::test(BuscaConsumidor::class)
            ->set('codpes', 'abc')
            ->call('buscar')
            ->assertHasErrors(['codpes']);
    }

    /** @test */
    public function carrinho_pedido_dispatches_error_toast_when_cart_is_empty(): void
    {
        Livewire::test(CarrinhoPedido::class)
            ->call('finalizarPedido')
            ->assertDispatched('toast', function ($event) {
                return $event['type'] === 'error'
                    && str_contains($event['message'], 'Adicione ao menos um produto');
            });
    }

    /** @test */
    public function carrinho_pedido_dispatches_error_toast_when_saldo_insufficient(): void
    {
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create(['valor' => 1000]);

        Livewire::test(CarrinhoPedido::class)
            ->set('codpes', $consumidor->codpes)
            ->set('saldoDisponivel', 500)
            ->set('itens', [$produto->id => 1])
            ->call('finalizarPedido')
            ->assertDispatched('toast', function ($event) {
                return $event['type'] === 'error'
                    && str_contains($event['message'], 'Saldo insuficiente');
            });
    }

    /** @test */
    public function lista_pedidos_dispatches_success_toast_when_marking_as_delivered(): void
    {
        $pedido = Pedido::factory()->create(['estado' => 'REALIZADO']);

        Livewire::test(ListaPedidosPendentes::class)
            ->call('marcarComoEntregue', $pedido->id)
            ->assertDispatched('toast', function ($event) {
                return $event['type'] === 'success'
                    && str_contains($event['message'], 'marcado como entregue');
            });

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'ENTREGUE',
        ]);
    }

    /** @test */
    public function toast_events_have_required_properties(): void
    {
        Livewire::test(CarrinhoPedido::class)
            ->call('finalizarPedido')
            ->assertDispatched('toast', function ($event) {
                return isset($event['type']) && isset($event['message']);
            });
    }

    /** @test */
    public function toast_type_is_one_of_valid_types(): void
    {
        $validTypes = ['success', 'error', 'warning', 'info'];

        Livewire::test(CarrinhoPedido::class)
            ->call('finalizarPedido')
            ->assertDispatched('toast', function ($event) use ($validTypes) {
                return in_array($event['type'], $validTypes);
            });
    }
}
