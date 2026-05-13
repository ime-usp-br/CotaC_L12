<?php

namespace Tests\Unit\Models;

use App\Models\CotaRegular;
use OwenIt\Auditing\Contracts\Auditable;
use Tests\TestCase;

class CotaRegularTest extends TestCase
{
    /**
     * Testa que fillable contém os campos esperados.
     */
    public function test_fillable_contem_vinculo_e_valor(): void
    {
        $cota = new CotaRegular;

        $this->assertEquals(['vinculo', 'valor'], $cota->getFillable());
    }

    /**
     * Testa que a tabela é cota_regulares.
     */
    public function test_tabela_e_cota_regulares(): void
    {
        $cota = new CotaRegular;

        $this->assertEquals('cota_regulares', $cota->getTable());
    }

    /**
     * Testa que o modelo implementa Auditable.
     */
    public function test_implements_auditable(): void
    {
        $cota = new CotaRegular;

        $this->assertInstanceOf(Auditable::class, $cota);
    }
}
