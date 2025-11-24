<?php

namespace App\Livewire;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class ListaPedidosPendentes extends Component
{
    public function render()
    {
        return view('livewire.lista-pedidos-pendentes', [
            'pedidos' => $this->getPedidosPendentes(),
        ]);
    }

    public function getPedidosPendentes(): Collection
    {
        return Pedido::where('estado', 'REALIZADO')
            ->with(['consumidor', 'itens.produto'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function marcarComoEntregue(int $pedidoId): void
    {
        $pedido = Pedido::find($pedidoId);

        if ($pedido && $pedido->estado === 'REALIZADO') {
            $pedido->estado = 'ENTREGUE';
            $pedido->save();

            // Opcional: Emitir evento ou flash message
            // session()->flash('message', 'Pedido marcado como entregue!');
        }
    }
}
