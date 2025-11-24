<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para o fluxo de entrega de pedidos.
 */
class EntregaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa que a rota de entregas pendentes retorna a view correta.
     */
    public function test_rota_entregas_retorna_view(): void
    {
        $response = $this->get(route('entregas.pendentes'));

        $response->assertOk();
        $response->assertViewIs('entregas.index');
        $response->assertSeeLivewire('lista-pedidos-pendentes');
    }
}
