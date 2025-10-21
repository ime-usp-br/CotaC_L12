<?php

namespace App\Services;

use App\Models\Consumidor;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Serviço responsável pela criação e gerenciamento de pedidos.
 *
 * Implementa a lógica de negócio para criar pedidos com seus itens
 * dentro de transações de banco de dados para garantir integridade.
 */
class PedidoService
{
    /**
     * Cria um novo pedido para um consumidor com os produtos especificados.
     *
     * Este método executa, dentro de uma transação de banco de dados,
     * a criação do registro de Pedido e dos respectivos ItemPedido.
     * O pedido é criado com estado inicial 'REALIZADO'.
     *
     * @param  Consumidor  $consumidor  O consumidor que está realizando o pedido.
     * @param  array<int, array{id: int, quantidade: int}>  $produtos  Array de produtos com id e quantidade.
     * @return Pedido O pedido criado com seus itens carregados.
     *
     * @throws \Exception Se houver erro durante a criação (rollback automático).
     */
    public function criarPedido(Consumidor $consumidor, array $produtos): Pedido
    {
        return DB::transaction(function () use ($consumidor, $produtos) {
            // Cria o pedido com estado inicial REALIZADO
            $pedido = Pedido::create([
                'consumidor_codpes' => $consumidor->codpes,
                'estado' => 'REALIZADO',
            ]);

            Log::info("PedidoService: Created pedido #{$pedido->id} for codpes {$consumidor->codpes}.");

            // Cria os itens do pedido
            foreach ($produtos as $produto) {
                $pedido->itens()->create([
                    'produto_id' => $produto['id'],
                    'quantidade' => $produto['quantidade'],
                ]);

                Log::debug("PedidoService: Added item to pedido #{$pedido->id}: produto_id={$produto['id']}, quantidade={$produto['quantidade']}.");
            }

            // Carrega os relacionamentos para retornar o pedido completo
            $pedido->load(['itens.produto', 'consumidor']);

            Log::info("PedidoService: Successfully created pedido #{$pedido->id} with ".$pedido->itens->count().' items.');

            return $pedido;
        });
    }
}
