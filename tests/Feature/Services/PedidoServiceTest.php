<?php

namespace Tests\Feature\Services;

use App\Models\Consumidor;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\PedidoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class PedidoServiceTest extends TestCase
{
    private PedidoService $pedidoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pedidoService = new PedidoService();
    }

    /**
     * Testa que a transação é revertida quando ocorre erro na criação dos itens.
     */
    public function test_rollback_transacao_quando_produto_nao_existe(): void
    {
        // 1. Arrange
        $consumidor = Consumidor::factory()->create();
        $produtoValido = Produto::factory()->create(['valor' => 10]);
        
        // Dados do pedido com um produto válido e um inexistente
        $produtos = [
            ['id' => $produtoValido->id, 'quantidade' => 1],
            ['id' => 99999, 'quantidade' => 2], // ID inexistente
        ];

        // 2. Act & Assert
        try {
            $this->pedidoService->criarPedido($consumidor, $produtos);
            $this->fail('Deveria ter lançado ModelNotFoundException');
        } catch (ModelNotFoundException $e) {
            // Esperado
        }

        // 3. Assert (Database Side Effects)
        
        // Verifica se NENHUM pedido foi criado para este consumidor
        $this->assertDatabaseMissing('pedidos', [
            'consumidor_codpes' => $consumidor->codpes,
        ]);

        // Verifica se NENHUM item foi criado (nem do produto válido)
        $this->assertDatabaseCount('item_pedidos', 0);
    }

    /**
     * Testa criação bem sucedida para garantir que o teste acima não é falso positivo.
     */
    public function test_cria_pedido_com_sucesso(): void
    {
        // 1. Arrange
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create(['valor' => 10]);
        
        $produtos = [
            ['id' => $produto->id, 'quantidade' => 2],
        ];

        // 2. Act
        $pedido = $this->pedidoService->criarPedido($consumidor, $produtos);

        // 3. Assert
        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id]);
        $this->assertDatabaseHas('item_pedidos', [
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'valor_unitario' => 10,
        ]);
    }
}
