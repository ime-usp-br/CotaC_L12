<?php

namespace Tests\Unit\Services;

use App\Models\Consumidor;
use App\Models\CotaEspecial;
use App\Models\CotaRegular;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use App\Services\CotaService;
use Tests\Fakes\FakeReplicadoService;
use Tests\TestCase;

class CotaServiceTest extends TestCase
{
    private CotaService $cotaService;

    private FakeReplicadoService $fakeReplicado;

    protected function setUp(): void
    {
        parent::setUp();

        // Configura o Fake Replicado Service
        $this->fakeReplicado = new FakeReplicadoService;
        $this->cotaService = new CotaService($this->fakeReplicado);
    }

    /**
     * Testa o cálculo de saldo para consumidor com cota especial.
     */
    public function test_calcula_saldo_com_cota_especial(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 123456,
            'nome' => 'João Silva',
        ]);

        CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 50,
        ]);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert
        $this->assertEquals(50, $resultado['cota']);
        $this->assertEquals(0, $resultado['gasto']);
        $this->assertEquals(50, $resultado['saldo']);
    }

    /**
     * Testa o cálculo de saldo para consumidor com cota regular (único vínculo).
     */
    public function test_calcula_saldo_com_cota_regular_unico_vinculo(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 654321,
            'nome' => 'Maria Santos',
        ]);

        CotaRegular::create([
            'vinculo' => 'SERVIDOR',
            'valor' => 30,
        ]);

        $this->fakeReplicado->setVinculos(654321, 8, ['SERVIDOR']);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert
        $this->assertEquals(30, $resultado['cota']);
        $this->assertEquals(0, $resultado['gasto']);
        $this->assertEquals(30, $resultado['saldo']);
    }

    /**
     * Testa o cálculo de saldo para consumidor com múltiplos vínculos (deve usar a maior cota).
     */
    public function test_calcula_saldo_com_multiplos_vinculos_usa_maior_cota(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 111222,
            'nome' => 'Pedro Oliveira',
        ]);

        CotaRegular::create([
            'vinculo' => 'SERVIDOR',
            'valor' => 30,
        ]);

        CotaRegular::create([
            'vinculo' => 'ALUNOPOS',
            'valor' => 20,
        ]);

        CotaRegular::create([
            'vinculo' => 'DOCENTE',
            'valor' => 40,
        ]);

        $this->fakeReplicado->setVinculos(111222, 8, ['SERVIDOR', 'ALUNOPOS', 'DOCENTE']);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert - Deve usar a maior cota (DOCENTE = 40)
        $this->assertEquals(40, $resultado['cota']);
        $this->assertEquals(0, $resultado['gasto']);
        $this->assertEquals(40, $resultado['saldo']);
    }

    /**
     * Testa o cálculo de saldo para consumidor sem cota (nenhum vínculo).
     */
    public function test_calcula_saldo_sem_cota_nenhum_vinculo(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 999888,
            'nome' => 'Ana Costa',
        ]);

        // Não configura vínculos no fake (retornará array vazio)
        $this->fakeReplicado->setVinculos(999888, 8, []);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert
        $this->assertEquals(0, $resultado['cota']);
        $this->assertEquals(0, $resultado['gasto']);
        $this->assertEquals(0, $resultado['saldo']);
    }

    /**
     * Testa o cálculo de saldo para consumidor com vínculos, mas sem cota regular cadastrada.
     */
    public function test_calcula_saldo_sem_cota_regular_cadastrada(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 777666,
            'nome' => 'Carlos Ferreira',
        ]);

        // Configura vínculo, mas não cadastra cota regular correspondente
        $this->fakeReplicado->setVinculos(777666, 8, ['ESTAGIARIO']);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert
        $this->assertEquals(0, $resultado['cota']);
        $this->assertEquals(0, $resultado['gasto']);
        $this->assertEquals(0, $resultado['saldo']);
    }

    /**
     * Testa o cálculo de saldo com gastos no mês atual.
     */
    public function test_calcula_saldo_com_gastos_no_mes_atual(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 555444,
            'nome' => 'Fernanda Lima',
        ]);

        CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 50,
        ]);

        $produto1 = Produto::factory()->create(['valor' => 5]);
        $produto2 = Produto::factory()->create(['valor' => 10]);

        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto1->id,
            'quantidade' => 2, // 2 x 5 = 10
            'valor_unitario' => 5,
        ]);

        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto2->id,
            'quantidade' => 1, // 1 x 10 = 10
            'valor_unitario' => 10,
        ]);

        // Total gasto = 10 + 10 = 20

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert
        $this->assertEquals(50, $resultado['cota']);
        $this->assertEquals(20, $resultado['gasto']);
        $this->assertEquals(30, $resultado['saldo']);
    }

    /**
     * Testa o cálculo de saldo negativo quando gasto excede a cota.
     */
    public function test_calcula_saldo_negativo_quando_gasto_excede_cota(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 333222,
            'nome' => 'Roberto Alves',
        ]);

        CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 20,
        ]);

        $produto = Produto::factory()->create(['valor' => 15]);

        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2, // 2 x 15 = 30 (excede cota de 20)
            'valor_unitario' => 15,
        ]);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert
        $this->assertEquals(20, $resultado['cota']);
        $this->assertEquals(30, $resultado['gasto']);
        $this->assertEquals(-10, $resultado['saldo']);
    }

    /**
     * Testa que apenas pedidos do mês atual são considerados no cálculo.
     */
    public function test_calcula_saldo_considera_apenas_mes_atual(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 888999,
            'nome' => 'Juliana Souza',
        ]);

        CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 50,
        ]);

        $produto = Produto::factory()->create(['valor' => 10]);

        // Pedido do mês passado (não deve ser contabilizado)
        $pedidoMesPassado = new Pedido([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);
        $pedidoMesPassado->created_at = now()->subMonth();
        $pedidoMesPassado->updated_at = now()->subMonth();
        $pedidoMesPassado->save();

        ItemPedido::create([
            'pedido_id' => $pedidoMesPassado->id,
            'produto_id' => $produto->id,
            'quantidade' => 3, // 3 x 10 = 30 (mês passado)
            'valor_unitario' => 10,
        ]);

        // Pedido do mês atual
        $pedidoMesAtual = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        ItemPedido::create([
            'pedido_id' => $pedidoMesAtual->id,
            'produto_id' => $produto->id,
            'quantidade' => 1, // 1 x 10 = 10 (mês atual)
            'valor_unitario' => 10,
        ]);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert - Deve considerar apenas o pedido do mês atual
        $this->assertEquals(50, $resultado['cota']);
        $this->assertEquals(10, $resultado['gasto']);
        $this->assertEquals(40, $resultado['saldo']);
    }

    /**
     * Testa que cota especial tem prioridade sobre cota regular.
     */
    public function test_cota_especial_tem_prioridade_sobre_cota_regular(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 666555,
            'nome' => 'Ricardo Mendes',
        ]);

        // Cadastra cota especial
        CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 100,
        ]);

        // Cadastra cota regular (que seria aplicável)
        CotaRegular::create([
            'vinculo' => 'DOCENTE',
            'valor' => 40,
        ]);

        $this->fakeReplicado->setVinculos(666555, 8, ['DOCENTE']);

        // Act
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        // Assert - Deve usar cota especial (100), não a regular (40)
        $this->assertEquals(100, $resultado['cota']);
        $this->assertEquals(0, $resultado['gasto']);
        $this->assertEquals(100, $resultado['saldo']);
    }

    /**
     * Testa que alteração de preço de produto não afeta pedidos anteriores.
     *
     * Este teste verifica o fix do bug #42, onde mudanças no preço de produtos
     * causavam alteração retroativa no histórico de gastos.
     */
    public function test_alteracao_preco_produto_nao_afeta_pedidos_anteriores(): void
    {
        // Arrange
        $consumidor = Consumidor::factory()->create([
            'codpes' => 123456,
            'nome' => 'João Silva',
        ]);

        CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 100,
        ]);

        $produto = Produto::factory()->create(['valor' => 10]);

        // Cria pedido com produto a R$ 10
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'valor_unitario' => 10, // Snapshot: 2 x 10 = 20
        ]);

        // Act: Altera o preço do produto para R$ 20
        $produto->update(['valor' => 20]);

        // Assert: Gasto mensal deve continuar sendo 20, não 40
        $resultado = $this->cotaService->calcularSaldoParaConsumidor($consumidor);

        $this->assertEquals(100, $resultado['cota']);
        $this->assertEquals(20, $resultado['gasto']); // ✅ Deve usar preço histórico
        $this->assertEquals(80, $resultado['saldo']);
    }
}
