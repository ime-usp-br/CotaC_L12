<?php

namespace Tests\Feature;

use App\Models\Consumidor;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para o fluxo de entrega de pedidos.
 */
class EntregaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa que a listagem de pedidos pendentes retorna apenas pedidos com estado REALIZADO.
     */
    public function test_lista_pedidos_pendentes(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create();

        // Criar pedido REALIZADO (deve aparecer)
        $pedidoPendente = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        $pedidoPendente->itens()->create([
            'produto_id' => $produto->id,
            'quantidade' => 2,
        ]);

        // Criar pedido ENTREGUE (não deve aparecer)
        $pedidoEntregue = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'ENTREGUE',
        ]);
        $pedidoEntregue->itens()->create([
            'produto_id' => $produto->id,
            'quantidade' => 1,
        ]);

        // Act
        $response = $this->getJson('/entregas/pendentes');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'consumidor' => ['codpes', 'nome'],
                    'estado',
                    'itens',
                    'created_at',
                ],
            ],
        ]);

        $data = $response->json('data');
        $this->assertCount(1, $data); // Apenas 1 pedido pendente
        $this->assertEquals($pedidoPendente->id, $data[0]['id']);
        $this->assertEquals('REALIZADO', $data[0]['estado']);
    }

    /**
     * Testa que marcar um pedido como entregue altera o estado corretamente.
     */
    public function test_marca_pedido_como_entregue(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create();
        $pedido = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        // Act
        $response = $this->putJson("/entregas/{$pedido->id}");

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'data' => [
                'pedido_id',
                'estado',
            ],
        ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'ENTREGUE',
        ]);

        $response->assertJson([
            'data' => [
                'pedido_id' => $pedido->id,
                'estado' => 'ENTREGUE',
            ],
        ]);
    }

    /**
     * Testa que pedidos entregues não aparecem na lista de pendentes.
     */
    public function test_pedido_entregue_nao_aparece_na_lista(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create();
        $produto = Produto::factory()->create();

        $pedido = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        $pedido->itens()->create([
            'produto_id' => $produto->id,
            'quantidade' => 1,
        ]);

        // Verificar que aparece na lista inicialmente
        $response1 = $this->getJson('/entregas/pendentes');
        $response1->assertOk();
        $this->assertCount(1, $response1->json('data'));

        // Act - Marcar como entregue
        $this->putJson("/entregas/{$pedido->id}");

        // Assert - Verificar que não aparece mais na lista
        $response2 = $this->getJson('/entregas/pendentes');
        $response2->assertOk();
        $this->assertCount(0, $response2->json('data'));
    }

    /**
     * Testa que a listagem retorna a estrutura JSON correta com dados do consumidor e itens.
     */
    public function test_lista_pedidos_pendentes_retorna_estrutura_json_correta(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 123456,
            'nome' => 'João Silva',
        ]);
        $produto = Produto::factory()->create([
            'nome' => 'Café',
            'valor' => 5,
        ]);

        $pedido = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        $pedido->itens()->create([
            'produto_id' => $produto->id,
            'quantidade' => 2,
        ]);

        // Act
        $response = $this->getJson('/entregas/pendentes');

        // Assert
        $response->assertOk();
        $data = $response->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals($pedido->id, $data[0]['id']);
        $this->assertEquals('REALIZADO', $data[0]['estado']);

        // Verificar dados do consumidor
        $this->assertEquals(123456, $data[0]['consumidor']['codpes']);
        $this->assertEquals('João Silva', $data[0]['consumidor']['nome']);

        // Verificar itens do pedido
        $this->assertCount(1, $data[0]['itens']);
        $this->assertEquals($produto->id, $data[0]['itens'][0]['produto_id']);
        $this->assertEquals('Café', $data[0]['itens'][0]['produto_nome']);
        $this->assertEquals(2, $data[0]['itens'][0]['quantidade']);
        $this->assertEquals(5, $data[0]['itens'][0]['valor_unitario']);
        $this->assertEquals(10, $data[0]['itens'][0]['valor_total']);
    }
}
