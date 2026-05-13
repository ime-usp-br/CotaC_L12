<?php

namespace Tests\Unit\Services;

use App\Exceptions\ReplicadoServiceException;
use App\Services\ReplicadoService;
use Mockery;
use Tests\TestCase;

class ReplicadoServiceRealTest extends TestCase
{
    private ReplicadoService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Uspdev\Replicado\Pessoa class before loading ReplicadoService
        $this->pessoaMock = Mockery::mock('overload:Uspdev\Replicado\Pessoa');
        $this->service = new ReplicadoService;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_buscar_pessoa_retorna_dados_quando_encontrada(): void
    {
        $this->pessoaMock->shouldReceive('dump')
            ->with(123456)
            ->andReturn(['nompes' => 'João Silva']);
        $this->pessoaMock->shouldReceive('retornarEmailUsp')
            ->with(123456)
            ->andReturn('joao@usp.br');

        $resultado = $this->service->buscarPessoa(123456);

        $this->assertNotNull($resultado);
        $this->assertEquals('João Silva', $resultado['nompes']);
        $this->assertEquals('joao@usp.br', $resultado['emailusp']);
    }

    public function test_buscar_pessoa_retorna_null_quando_nao_encontrada(): void
    {
        $this->pessoaMock->shouldReceive('dump')
            ->with(999999)
            ->andReturn([]);

        $resultado = $this->service->buscarPessoa(999999);

        $this->assertNull($resultado);
    }

    public function test_buscar_pessoa_lanca_excecao_quando_replicado_falha(): void
    {
        $this->pessoaMock->shouldReceive('dump')
            ->with(123456)
            ->andThrow(new \Exception('DB Error'));

        $this->expectException(ReplicadoServiceException::class);

        $this->service->buscarPessoa(123456);
    }

    public function test_obter_vinculos_ativos_retorna_vinculos(): void
    {
        $this->pessoaMock->shouldReceive('obterSiglasVinculosAtivos')
            ->with(123456)
            ->andReturn(['SERVIDOR', 'ALUNOPOS']);

        $resultado = $this->service->obterVinculosAtivos(123456, 45);

        $this->assertEquals(['SERVIDOR', 'ALUNOPOS'], $resultado);
    }

    public function test_obter_vinculos_ativos_retorna_array_vazio_quando_sem_vinculos(): void
    {
        $this->pessoaMock->shouldReceive('obterSiglasVinculosAtivos')
            ->with(123456)
            ->andReturn([]);

        $resultado = $this->service->obterVinculosAtivos(123456, 45);

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    public function test_obter_vinculos_ativos_lanca_excecao_quando_falha(): void
    {
        $this->pessoaMock->shouldReceive('obterSiglasVinculosAtivos')
            ->with(123456)
            ->andThrow(new \Exception('DB Error'));

        $this->expectException(ReplicadoServiceException::class);

        $this->service->obterVinculosAtivos(123456, 45);
    }

    public function test_validar_nusp_email_retorna_true_quando_corresponde(): void
    {
        $this->pessoaMock->shouldReceive('emails')
            ->with(123456)
            ->andReturn(['joao@usp.br']);

        $resultado = $this->service->validarNuspEmail(123456, 'joao@usp.br');

        $this->assertTrue($resultado);
    }

    public function test_validar_nusp_email_retorna_false_quando_nao_corresponde(): void
    {
        $this->pessoaMock->shouldReceive('emails')
            ->with(123456)
            ->andReturn(['outro@usp.br']);

        $resultado = $this->service->validarNuspEmail(123456, 'joao@usp.br');

        $this->assertFalse($resultado);
    }

    public function test_validar_nusp_email_retorna_false_quando_sem_emails(): void
    {
        $this->pessoaMock->shouldReceive('emails')
            ->with(123456)
            ->andReturn([]);

        $resultado = $this->service->validarNuspEmail(123456, 'joao@usp.br');

        $this->assertFalse($resultado);
    }

    public function test_validar_nusp_email_lanca_excecao_quando_falha(): void
    {
        $this->pessoaMock->shouldReceive('emails')
            ->with(123456)
            ->andThrow(new \Exception('DB Error'));

        $this->expectException(ReplicadoServiceException::class);

        $this->service->validarNuspEmail(123456, 'joao@usp.br');
    }

    public function test_obter_vinculos_ativos_restaura_env_original(): void
    {
        putenv('REPLICADO_CODUNDCLG=99');

        $this->pessoaMock->shouldReceive('obterSiglasVinculosAtivos')
            ->with(123456)
            ->andReturnUsing(function () {
                $this->assertEquals('45', getenv('REPLICADO_CODUNDCLG'));

                return ['SERVIDOR'];
            });

        $this->service->obterVinculosAtivos(123456, 45);

        $this->assertEquals('99', getenv('REPLICADO_CODUNDCLG'));
    }
}
