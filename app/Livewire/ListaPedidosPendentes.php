<?php

namespace App\Livewire;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ListaPedidosPendentes extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.lista-pedidos-pendentes', [
            'pedidos' => $this->getPedidosPendentes(),
        ]);
    }

    /**
     * @return Collection<int, \App\Models\Pedido>
     */
    public function getPedidosPendentes(): Collection
    {
        return Pedido::where('estado', Pedido::ESTADO_REALIZADO)
            ->where('created_at', '>', now()->subHour())
            ->with(['consumidor', 'itens.produto'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function marcarComoEntregue(int $pedidoId): void
    {
        $pedido = Pedido::find($pedidoId);

        if ($pedido && $pedido->estado === Pedido::ESTADO_REALIZADO) {
            $pedido->estado = Pedido::ESTADO_ENTREGUE;
            $pedido->save();

            // Dispatch toast notification
            $this->dispatch('toast', type: 'success', message: __('Pedido marcado como entregue!'));
        }
    }
}
