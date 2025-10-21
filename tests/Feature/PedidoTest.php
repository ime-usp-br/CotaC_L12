<?php

namespace Tests\Feature;

use App\Models\Consumidor;
use App\Models\CotaRegular;
use App\Models\Produto;
use App\Services\ReplicadoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fakes\FakeReplicadoService;
use Tests\TestCase;

/**
 * Testes para o fluxo de realização de pedido no balcão.
 */
class PedidoTest extends TestCase
{
    use RefreshDatabase;

    private FakeReplicadoService $fakeReplicado;

    protected function setUp(): void
    {
        parent::setUp();

        // Configura o Fake Replicado Service
        $this->fakeReplicado = new FakeReplicadoService;
        $this->app->instance(ReplicadoService::class, $this->fakeReplicado);
    }

    /**
     * Testa a criação de pedido com dados válidos.
     */
    public function test_cria_pedido_com_dados_validos(): void
    {
        // Arrange
        $codpes = 123456;
        $this->fakeReplicado->setPessoa($codpes, [
            'nompes' => 'João Silva',
            'emailusp' => 'joao@usp.br',
        ]);

        // Configurar cota regular para o vínculo
        CotaRegular::create([
            'vinculo' => 'SERVIDOR',
            'valor' => 50,
        ]);

        $this->fakeReplicado->setVinculos($codpes, 8, ['SERVIDOR']);

        // Criar produtos
        $produto1 = Produto::factory()->create(['nome' => 'Café', 'valor' => 5]);
        $produto2 = Produto::factory()->create(['nome' => 'Suco', 'valor' => 10]);

        $payload = [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto1->id, 'quantidade' => 2], // 10
                ['id' => $produto2->id, 'quantidade' => 1], // 10
            ],
        ];

        // Act
        $response = $this->postJson('/pedidos', $payload);

        // Assert
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'pedido_id',
                'consumidor' => ['codpes', 'nome'],
                'estado',
                'itens',
                'created_at',
            ],
        ]);

        $this->assertDatabaseHas('pedidos', [
            'consumidor_codpes' => $codpes,
            'estado' => 'REALIZADO',
        ]);

        $this->assertDatabaseHas('item_pedidos', [
            'produto_id' => $produto1->id,
            'quantidade' => 2,
        ]);

        $this->assertDatabaseHas('item_pedidos', [
            'produto_id' => $produto2->id,
            'quantidade' => 1,
        ]);

        $this->assertDatabaseHas('consumidores', [
            'codpes' => $codpes,
            'nome' => 'João Silva',
        ]);
    }

    /**
     * Testa erro de validação quando codpes não é encontrado no Replicado.
     */
    public function test_retorna_erro_quando_codpes_invalido(): void
    {
        // Arrange
        $codpesInvalido = 999999;

        // Não configurar pessoa no fake = retorna null
        $produto = Produto::factory()->create();

        $payload = [
            'codpes' => $codpesInvalido,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ];

        // Act
        $response = $this->postJson('/pedidos', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['codpes']);

        $this->assertDatabaseMissing('pedidos', [
            'consumidor_codpes' => $codpesInvalido,
        ]);
    }

    /**
     * Testa erro de validação quando saldo é insuficiente.
     */
    public function test_retorna_erro_quando_saldo_insuficiente(): void
    {
        // Arrange
        $codpes = 654321;
        $this->fakeReplicado->setPessoa($codpes, [
            'nompes' => 'Maria Santos',
            'emailusp' => 'maria@usp.br',
        ]);

        // Configurar cota baixa
        CotaRegular::create([
            'vinculo' => 'ALUNOPOS',
            'valor' => 10, // Cota de apenas 10
        ]);

        $this->fakeReplicado->setVinculos($codpes, 8, ['ALUNOPOS']);

        // Criar produto caro
        $produto = Produto::factory()->create(['nome' => 'Produto Caro', 'valor' => 20]);

        $payload = [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1], // Total: 20, saldo: 10
            ],
        ];

        // Act
        $response = $this->postJson('/pedidos', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => ['saldo'],
        ]);

        $this->assertDatabaseMissing('pedidos', [
            'consumidor_codpes' => $codpes,
        ]);
    }

    /**
     * Testa erro de validação quando produtos estão vazios.
     */
    public function test_retorna_erro_quando_produtos_vazios(): void
    {
        // Arrange
        $payload = [
            'codpes' => 123456,
            'produtos' => [],
        ];

        // Act
        $response = $this->postJson('/pedidos', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['produtos']);
    }

    /**
     * Testa erro de validação quando produto não existe.
     */
    public function test_retorna_erro_quando_produto_nao_existe(): void
    {
        // Arrange
        $codpes = 123456;
        $this->fakeReplicado->setPessoa($codpes, [
            'nompes' => 'João Silva',
            'emailusp' => 'joao@usp.br',
        ]);

        $payload = [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => 99999, 'quantidade' => 1], // ID inexistente
            ],
        ];

        // Act
        $response = $this->postJson('/pedidos', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['produtos.0.id']);
    }

    /**
     * Testa erro de validação quando quantidade é inválida.
     */
    public function test_retorna_erro_quando_quantidade_invalida(): void
    {
        // Arrange
        $produto = Produto::factory()->create();

        $payload = [
            'codpes' => 123456,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 0], // Quantidade inválida
            ],
        ];

        // Act
        $response = $this->postJson('/pedidos', $payload);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['produtos.0.quantidade']);
    }

    /**
     * Testa que pedido deduz do saldo disponível.
     */
    public function test_pedido_deduz_do_saldo_disponivel(): void
    {
        // Arrange
        $codpes = 111222;
        $this->fakeReplicado->setPessoa($codpes, [
            'nompes' => 'Pedro Oliveira',
            'emailusp' => 'pedro@usp.br',
        ]);

        CotaRegular::create([
            'vinculo' => 'SERVIDOR',
            'valor' => 50,
        ]);

        $this->fakeReplicado->setVinculos($codpes, 8, ['SERVIDOR']);

        $produto = Produto::factory()->create(['valor' => 15]);

        // Primeiro pedido
        $payload1 = [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 2], // 30
            ],
        ];

        $response1 = $this->postJson('/pedidos', $payload1);
        $response1->assertStatus(201);

        // Segundo pedido (deve passar, saldo restante = 20)
        $payload2 = [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1], // 15
            ],
        ];

        $response2 = $this->postJson('/pedidos', $payload2);
        $response2->assertStatus(201);

        // Terceiro pedido (deve falhar, saldo restante = 5)
        $payload3 = [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1], // 15 > 5
            ],
        ];

        $response3 = $this->postJson('/pedidos', $payload3);
        $response3->assertStatus(422);
        $response3->assertJsonValidationErrors(['saldo']);
    }

    /**
     * Testa que cota especial tem prioridade sobre cota regular.
     */
    public function test_cota_especial_tem_prioridade(): void
    {
        // Arrange
        $codpes = 777888;
        $this->fakeReplicado->setPessoa($codpes, [
            'nompes' => 'Ana Costa',
            'emailusp' => 'ana@usp.br',
        ]);

        // Criar consumidor e cota especial
        $consumidor = Consumidor::create([
            'codpes' => $codpes,
            'nome' => 'Ana Costa',
        ]);

        $consumidor->cotaEspecial()->create([
            'valor' => 100, // Cota especial
        ]);

        // Cota regular menor
        CotaRegular::create([
            'vinculo' => 'ALUNOPOS',
            'valor' => 20,
        ]);

        $this->fakeReplicado->setVinculos($codpes, 8, ['ALUNOPOS']);

        $produto = Produto::factory()->create(['valor' => 80]);

        $payload = [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1], // 80 <= 100
            ],
        ];

        // Act
        $response = $this->postJson('/pedidos', $payload);

        // Assert
        $response->assertStatus(201); // Deve passar pois usa cota especial de 100
    }
}
