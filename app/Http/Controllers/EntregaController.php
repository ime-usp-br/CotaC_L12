<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\JsonResponse;

/**
 * Controlador para gerenciar a entrega de pedidos.
 *
 * Responsável pela interface pública onde atendentes
 * visualizam pedidos pendentes e confirmam entregas.
 */
class EntregaController extends Controller
{
    /**
     * Lista todos os pedidos pendentes de entrega.
     *
     * Retorna uma view com a interface de entrega onde atendentes
     * podem visualizar pedidos pendentes e marcar como entregues.
     *
     * @return \Illuminate\View\View Interface de entrega de pedidos.
     */
    public function index()
    {
        return view('entregas.index');
    }

    /**
     * Marca um pedido como entregue.
     *
     * Altera o estado do pedido de 'REALIZADO' para 'ENTREGUE'.
     *
     * @param  Pedido  $pedido  O pedido a ser marcado como entregue (via route model binding).
     * @return JsonResponse Resposta de sucesso.
     */
    public function update(Pedido $pedido): JsonResponse
    {
        $pedido->estado = 'ENTREGUE';
        $pedido->save();

        return response()->json([
            'message' => __('Pedido marcado como entregue.'),
            'data' => [
                'pedido_id' => $pedido->id,
                'estado' => $pedido->estado,
            ],
        ]);
    }
}
