<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\StorePedidoRequest;
use App\Models\Consumidor;
use App\Models\CotaRegular;
use App\Models\Produto;
use App\Services\CotaService;
use App\Services\ReplicadoService;
use Illuminate\Support\Facades\Route;
use Tests\Fakes\FakeReplicadoService;
use Tests\TestCase;

class StorePedidoRequestTest extends TestCase
{
    private FakeReplicadoService $fakeReplicado;

    protected function setUp(): void
    {
        parent::setUp();

        // Configura Fake Replicado
        $this->fakeReplicado = new FakeReplicadoService();
        $this->app->instance(ReplicadoService::class, $this->fakeReplicado);

        // Mock config
        config(['replicado.codundclg' => 45]);

        // Define rota de teste
        Route::post('/test/store-pedido', function (StorePedidoRequest $request) {
            return response()->json(['status' => 'success']);
        });
    }

    /**
     * Testa validação de campos obrigatórios.
     */
    public function test_valida_campos_obrigatorios(): void
    {
        $response = $this->postJson('/test/store-pedido', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['codpes', 'produtos']);
    }

    /**
     * Testa validação de produto inexistente.
     */
    public function test_valida_produto_inexistente(): void
    {
        $response = $this->postJson('/test/store-pedido', [
            'codpes' => 123456,
            'produtos' => [
                ['id' => 99999, 'quantidade' => 1]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['produtos.0.id']);
    }

    /**
     * Testa validação de quantidade mínima.
     */
    public function test_valida_quantidade_minima(): void
    {
        $produto = Produto::factory()->create();

        $response = $this->postJson('/test/store-pedido', [
            'codpes' => 123456,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 0]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['produtos.0.quantidade']);
    }

    /**
     * Testa validação de número USP inexistente no Replicado.
     */
    public function test_valida_codpes_inexistente_no_replicado(): void
    {
        // Garante que o fake não conhece este usuário
        // $fakeReplicado inicializa vazio

        $produto = Produto::factory()->create();

        $response = $this->postJson('/test/store-pedido', [
            'codpes' => 123456,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['codpes']);
        
        $response->assertJsonFragment([
            'codpes' => [__('O Número USP informado não existe no sistema.')]
        ]);
    }

    /**
     * Testa validação de saldo insuficiente.
     */
    public function test_valida_saldo_insuficiente(): void
    {
        $codpes = 654321;
        $nome = 'Maria Saldo Insuficiente';
        
        // 1. Configura Fake Replicado com vínculo
        $this->fakeReplicado->setPessoa($codpes, ['nome' => $nome]);
        $this->fakeReplicado->setVinculos($codpes, 45, ['ALUNO']);

        // 2. Cria Consumidor
        $consumidor = Consumidor::factory()->create(['codpes' => $codpes, 'nome' => $nome]);

        // 3. Define Cota bem baixa (R$ 10)
        CotaRegular::create(['vinculo' => 'ALUNO', 'valor' => 10]);

        // 4. Cria produto caro (R$ 20)
        $produto = Produto::factory()->create(['valor' => 20]);

        // 5. Tenta comprar
        $response = $this->postJson('/test/store-pedido', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1]
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['saldo']);
            
        $response->assertJsonFragment([
            'saldo' => [__('Saldo insuficiente. Disponível: 10, Necessário: 20')]
        ]);
    }

    /**
     * Testa sucesso quando há saldo suficiente.
     */
    public function test_sucesso_com_saldo_suficiente(): void
    {
        $codpes = 111222;
        $nome = 'Pedro Rico';
        
        // 1. Configura Fake Replicado
        $this->fakeReplicado->setPessoa($codpes, ['nome' => $nome]);
        $this->fakeReplicado->setVinculos($codpes, 45, ['DOCENTE']);

        // 2. Cria Consumidor
        $consumidor = Consumidor::factory()->create(['codpes' => $codpes, 'nome' => $nome]);

        // 3. Define Cota alta (R$ 100)
        CotaRegular::create(['vinculo' => 'DOCENTE', 'valor' => 100]);

        // 4. Cria produto barato (R$ 10)
        $produto = Produto::factory()->create(['valor' => 10]);

        // 5. Compra
        $response = $this->postJson('/test/store-pedido', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1]
            ]
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }
}
