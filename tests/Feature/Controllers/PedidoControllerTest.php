<?php

namespace Tests\Feature\Controllers;

use App\Models\Consumidor;
use App\Models\CotaRegular;
use App\Models\Produto;
use App\Services\ReplicadoService;
use Tests\Fakes\FakeReplicadoService;
use Tests\TestCase;

class PedidoControllerTest extends TestCase
{
    private FakeReplicadoService $fakeReplicado;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeReplicado = new FakeReplicadoService;
        $this->app->instance(ReplicadoService::class, $this->fakeReplicado);

        config(['replicado.codundclg' => 8]);
    }

    /**
     * Testa que store cria um pedido com sucesso.
     */
    public function test_store_cria_pedido_com_sucesso(): void
    {
        $codpes = 123456;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'João Silva', 'emailusp' => 'joao@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['DOCENTE']);

        Consumidor::factory()->create(['codpes' => $codpes, 'nome' => 'João Silva']);
        CotaRegular::create(['vinculo' => 'DOCENTE', 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $response = $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'pedido_id',
                    'consumidor' => ['codpes', 'nome'],
                    'estado',
                    'itens',
                    'created_at',
                ],
            ]);
    }

    /**
     * Testa que store cria um consumidor novo quando não existe.
     */
    public function test_store_cria_consumidor_novo(): void
    {
        $codpes = 654321;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Maria Santos', 'emailusp' => 'maria@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['SERVIDOR']);

        CotaRegular::create(['vinculo' => 'SERVIDOR', 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $this->assertDatabaseMissing('consumidores', ['codpes' => $codpes]);

        $response = $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('consumidores', [
            'codpes' => $codpes,
            'nome' => 'Maria Santos',
        ]);
    }

    /**
     * Testa que store atualiza a categoria de um consumidor existente.
     */
    public function test_store_atualiza_categoria_do_consumidor_existente(): void
    {
        $codpes = 111222;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Pedro Oliveira', 'emailusp' => 'pedro@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['DOCENTE']);

        Consumidor::factory()->create(['codpes' => $codpes, 'nome' => 'Pedro Oliveira', 'categoria' => null]);
        CotaRegular::create(['vinculo' => 'DOCENTE', 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $this->assertDatabaseHas('consumidores', [
            'codpes' => $codpes,
            'categoria' => 'Docente',
        ]);
    }

    /**
     * Testa que store retorna JSON com estrutura correta.
     */
    public function test_store_retorna_json_com_estrutura_correta(): void
    {
        $codpes = 333444;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Ana Costa', 'emailusp' => 'ana@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['ALUNOPOS']);

        Consumidor::factory()->create(['codpes' => $codpes, 'nome' => 'Ana Costa']);
        CotaRegular::create(['vinculo' => 'ALUNOPOS', 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $response = $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $response->assertJsonStructure([
            'message',
            'data' => [
                'pedido_id',
                'consumidor' => ['codpes', 'nome'],
                'estado',
                'itens' => [
                    '*' => [
                        'produto_id',
                        'produto_nome',
                        'quantidade',
                        'valor_unitario',
                        'valor_total',
                    ],
                ],
                'created_at',
            ],
        ]);
    }

    /**
     * Testa que store retorna 422 para codpes inválido.
     */
    public function test_store_retorna_erro_422_para_codpes_invalido(): void
    {
        $produto = Produto::factory()->create();

        $response = $this->postJson('/pedidos', [
            'codpes' => 999999,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['codpes']);
    }

    /**
     * Testa que store retorna 422 para saldo insuficiente.
     */
    public function test_store_retorna_erro_422_para_saldo_insuficiente(): void
    {
        $codpes = 555666;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Carlos Ferreira', 'emailusp' => 'carlos@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['SERVIDOR']);

        Consumidor::factory()->create(['codpes' => $codpes, 'nome' => 'Carlos Ferreira']);
        CotaRegular::create(['vinculo' => 'SERVIDOR', 'valor' => 10]);
        $produto = Produto::factory()->create(['valor' => 20]);

        $response = $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['saldo']);
    }

    /**
     * Testa que store determina categoria Docente.
     */
    public function test_store_determina_categoria_docente(): void
    {
        $codpes = 777888;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Prof Teste', 'emailusp' => 'prof@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['DOCENTE']);

        CotaRegular::create(['vinculo' => 'DOCENTE', 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $this->assertDatabaseHas('consumidores', [
            'codpes' => $codpes,
            'categoria' => 'Docente',
        ]);
    }

    /**
     * Testa que store determina categoria Servidor.
     */
    public function test_store_determina_categoria_servidor(): void
    {
        $codpes = 999000;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Serv Teste', 'emailusp' => 'serv@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['SERVIDOR']);

        CotaRegular::create(['vinculo' => 'SERVIDOR', 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $this->assertDatabaseHas('consumidores', [
            'codpes' => $codpes,
            'categoria' => 'Servidor',
        ]);
    }

    /**
     * Testa que store determina categoria Aluno.
     */
    public function test_store_determina_categoria_aluno(): void
    {
        $codpes = 123123;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Aluno Teste', 'emailusp' => 'aluno@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, ['ALUNOPOS']);

        CotaRegular::create(['vinculo' => 'ALUNOPOS', 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $this->assertDatabaseHas('consumidores', [
            'codpes' => $codpes,
            'categoria' => 'Aluno',
        ]);
    }

    /**
     * Testa que store define categoria como null quando não há vínculos.
     */
    public function test_store_categoria_null_sem_vinculos(): void
    {
        $codpes = 456456;
        $this->fakeReplicado->setPessoa($codpes, ['nompes' => 'Sem Vinculo', 'emailusp' => 'sem@usp.br']);
        $this->fakeReplicado->setVinculos($codpes, 8, []);

        Consumidor::factory()->create(['codpes' => $codpes, 'nome' => 'Sem Vinculo']);
        \App\Models\CotaEspecial::create(['consumidor_codpes' => $codpes, 'valor' => 100]);
        $produto = Produto::factory()->create(['valor' => 10]);

        $this->postJson('/pedidos', [
            'codpes' => $codpes,
            'produtos' => [
                ['id' => $produto->id, 'quantidade' => 1],
            ],
        ]);

        $this->assertDatabaseHas('consumidores', [
            'codpes' => $codpes,
            'categoria' => null,
        ]);
    }
}
