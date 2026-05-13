<?php

namespace Tests\Unit\Models;

use App\Models\Consumidor;
use App\Models\CotaEspecial;
use OwenIt\Auditing\Contracts\Auditable;
use Tests\TestCase;

class CotaEspecialTest extends TestCase
{
    /**
     * Testa que o modelo tem a relação consumidor.
     */
    public function test_tem_relacao_consumidor(): void
    {
        $consumidor = Consumidor::factory()->create();
        $cota = CotaEspecial::create([
            'consumidor_codpes' => $consumidor->codpes,
            'valor' => 100,
        ]);

        $this->assertInstanceOf(Consumidor::class, $cota->consumidor);
        $this->assertEquals($consumidor->codpes, $cota->consumidor->codpes);
    }

    /**
     * Testa que fillable contém os campos esperados.
     */
    public function test_fillable_contem_consumidor_codpes_e_valor(): void
    {
        $cota = new CotaEspecial;

        $this->assertEquals(['consumidor_codpes', 'valor'], $cota->getFillable());
    }

    /**
     * Testa que a tabela é cota_especiais.
     */
    public function test_tabela_e_cota_especiais(): void
    {
        $cota = new CotaEspecial;

        $this->assertEquals('cota_especiais', $cota->getTable());
    }

    /**
     * Testa que o modelo implementa Auditable.
     */
    public function test_implements_auditable(): void
    {
        $cota = new CotaEspecial;

        $this->assertInstanceOf(Auditable::class, $cota);
    }
}
