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
     * Retorna uma coleção JSON de pedidos com estado 'REALIZADO',
     * incluindo os dados do consumidor e os itens do pedido.
     *
     * @return JsonResponse Lista de pedidos pendentes.
     */
    public function index(): JsonResponse
    {
        $pedidosPendentes = Pedido::where('estado', 'REALIZADO')
            ->with(['consumidor', 'itens.produto'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'data' => $pedidosPendentes->map(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'consumidor' => [
                        'codpes' => $pedido->consumidor->codpes,
                        'nome' => $pedido->consumidor->nome,
                    ],
                    'estado' => $pedido->estado,
                    'itens' => $pedido->itens->map(function ($item) {
                        $produto = $item->produto;
                        if ($produto === null) {
                            return [];
                        }

                        return [
                            'produto_id' => $item->produto_id,
                            'produto_nome' => $produto->nome,
                            'quantidade' => $item->quantidade,
                            'valor_unitario' => $produto->valor,
                            'valor_total' => $item->quantidade * $produto->valor,
                        ];
                    }),
                    'created_at' => $pedido->created_at?->toIso8601String(),
                ];
            }),
        ]);
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
