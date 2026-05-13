<?php

namespace Tests\Unit\Models;

use App\Models\Consumidor;
use App\Models\CotaEspecial;
use App\Models\Pedido;
use Tests\TestCase;

class ConsumidorTest extends TestCase
{
    /**
     * Testa que a chave primária é codpes.
     */
    public function test_tem_chave_primaria_codpes(): void
    {
        $consumidor = new Consumidor;

        $this->assertEquals('codpes', $consumidor->getKeyName());
    }

    /**
     * Testa que a chave primária não é auto-incrementável.
     */
    public function test_nao_e_auto_increment(): void
    {
        $consumidor = new Consumidor;

        $this->assertFalse($consumidor->getIncrementing());
    }

    /**
     * Testa que o modelo tem a relação cotaEspecial.
     */
    public function test_tem_relacao_cota_especial(): void
    {
        $consumidor = Consumidor::factory()->create();
        CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 50,
        ]);

        $this->assertInstanceOf(CotaEspecial::class, $consumidor->cotaEspecial);
        $this->assertEquals(50, $consumidor->cotaEspecial->valor);
    }

    /**
     * Testa que o modelo tem a relação pedidos.
     */
    public function test_tem_relacao_pedidos(): void
    {
        $consumidor = Consumidor::factory()->create();
        Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        $this->assertCount(1, $consumidor->pedidos);
        $this->assertInstanceOf(Pedido::class, $consumidor->pedidos->first());
    }

    /**
     * Testa que fillable contém os campos esperados.
     */
    public function test_fillable_contem_codpes_nome_categoria(): void
    {
        $consumidor = new Consumidor;

        $this->assertEquals(['codpes', 'nome', 'categoria'], $consumidor->getFillable());
    }
}
