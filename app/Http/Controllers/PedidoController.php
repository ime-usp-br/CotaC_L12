<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePedidoRequest;
use App\Models\Consumidor;
use App\Services\PedidoService;
use App\Services\ReplicadoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Controlador para gerenciar pedidos no balcão.
 *
 * Responsável pela interface pública onde consumidores
 * realizam pedidos sem autenticação.
 */
class PedidoController extends Controller
{
    /**
     * Cria uma nova instância do controlador.
     *
     * @param  ReplicadoService  $replicadoService  Serviço para validar dados no Replicado.
     * @param  PedidoService  $pedidoService  Serviço para criar pedidos.
     */
    public function __construct(
        private ReplicadoService $replicadoService,
        private PedidoService $pedidoService
    ) {}

    /**
     * Cria um novo pedido para um consumidor.
     *
     * @param  StorePedidoRequest  $request  Requisição validada.
     * @return JsonResponse Resposta JSON com os dados do pedido criado.
     */
    public function store(StorePedidoRequest $request): JsonResponse
    {
        /** @var int $codpes */
        $codpes = $request->validated('codpes');

        // Obter dados do consumidor via Replicado (validação já foi feita no FormRequest)
        $pessoaData = $this->replicadoService->buscarPessoa($codpes);

        // Criar ou obter consumidor local
        $consumidor = Consumidor::firstOrCreate(
            ['codpes' => $codpes],
            ['nome' => $pessoaData['nompes'] ?? 'Nome não informado']
        );

        /** @var array<int, array{id: int, quantidade: int}> $produtos */
        $produtos = $request->validated('produtos');

        Log::info("PedidoController: Processing order for codpes {$codpes}.", [
            'consumidor_id' => $consumidor->codpes,
            'produtos_count' => count($produtos),
        ]);

        // Criar pedido através do serviço
        $pedido = $this->pedidoService->criarPedido(
            $consumidor,
            $produtos
        );

        return response()->json([
            'message' => __('Pedido criado com sucesso.'),
            'data' => [
                'pedido_id' => $pedido->id,
                'consumidor' => [
                    'codpes' => $consumidor->codpes,
                    'nome' => $consumidor->nome,
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
            ],
        ], 201);
    }
}
