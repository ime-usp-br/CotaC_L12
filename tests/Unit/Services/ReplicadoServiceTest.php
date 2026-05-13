<?php

namespace Tests\Unit\Services;

use App\Exceptions\ReplicadoServiceException;
use App\Services\ReplicadoService;
use Tests\TestCase;

class ReplicadoServiceTest extends TestCase
{
    private ReplicadoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReplicadoService;
    }

    /**
     * Testa que buscarPessoa lança exceção quando o Replicado falha.
     */
    public function test_buscar_pessoa_lanca_excecao_quando_replicado_falha(): void
    {
        // Usa o Fake que estende ReplicadoService para simular falha
        $fake = new \Tests\Fakes\FakeReplicadoService;
        $fake->shouldFail();

        $this->expectException(ReplicadoServiceException::class);

        $fake->buscarPessoa(123456);
    }

    /**
     * Testa que obterVinculosAtivos lança exceção quando o Replicado falha.
     */
    public function test_obter_vinculos_ativos_lanca_excecao_quando_replicado_falha(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;
        $fake->shouldFail();

        $this->expectException(ReplicadoServiceException::class);

        $fake->obterVinculosAtivos(123456, 45);
    }

    /**
     * Testa que validarNuspEmail lança exceção quando o Replicado falha.
     */
    public function test_validar_nusp_email_lanca_excecao_quando_replicado_falha(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;
        $fake->shouldFail();

        $this->expectException(ReplicadoServiceException::class);

        $fake->validarNuspEmail(123456, 'test@usp.br');
    }

    /**
     * Testa que buscarPessoa retorna null quando pessoa não é encontrada.
     */
    public function test_buscar_pessoa_retorna_null_quando_nao_encontrada(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;

        $resultado = $fake->buscarPessoa(999999);

        $this->assertNull($resultado);
    }

    /**
     * Testa que validarNuspEmail retorna false quando email não corresponde.
     */
    public function test_validar_nusp_email_retorna_false_quando_email_nao_corresponde(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;
        $fake->shouldReturn(false);

        $resultado = $fake->validarNuspEmail(123456, 'outro@usp.br');

        $this->assertFalse($resultado);
    }

    /**
     * Testa que validarNuspEmail retorna true quando email corresponde.
     */
    public function test_validar_nusp_email_retorna_true_quando_corresponde(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;
        $fake->shouldReturn(true);

        $resultado = $fake->validarNuspEmail(123456, 'test@usp.br');

        $this->assertTrue($resultado);
    }

    /**
     * Testa que obterVinculosAtivos retorna array vazio quando não há vínculos.
     */
    public function test_obter_vinculos_ativos_retorna_array_vazio_quando_sem_vinculos(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;

        $resultado = $fake->obterVinculosAtivos(123456, 45);

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    /**
     * Testa que obterVinculosAtivos retorna os vínculos configurados.
     */
    public function test_obter_vinculos_ativos_retorna_vinculos_configurados(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;
        $fake->setVinculos(123456, 45, ['SERVIDOR', 'ALUNOPOS']);

        $resultado = $fake->obterVinculosAtivos(123456, 45);

        $this->assertEquals(['SERVIDOR', 'ALUNOPOS'], $resultado);
    }

    /**
     * Testa que buscarPessoa retorna os dados configurados.
     */
    public function test_buscar_pessoa_retorna_dados_configurados(): void
    {
        $fake = new \Tests\Fakes\FakeReplicadoService;
        $fake->setPessoa(123456, ['nompes' => 'João Teste', 'emailusp' => 'joao@usp.br']);

        $resultado = $fake->buscarPessoa(123456);

        $this->assertNotNull($resultado);
        $this->assertEquals('João Teste', $resultado['nompes']);
        $this->assertEquals('joao@usp.br', $resultado['emailusp']);
    }
}
