<?php

namespace Tests\Feature\Controllers;

use App\Models\Consumidor;
use App\Models\Pedido;
use Tests\TestCase;

class EntregaControllerTest extends TestCase
{
    /**
     * Testa que index retorna a view de entregas.
     */
    public function test_index_retorna_view_entregas(): void
    {
        $response = $this->get('/entregas/pendentes');

        $response->assertStatus(200)
            ->assertViewIs('entregas.index');
    }

    /**
     * Testa que update marca o pedido como entregue.
     */
    public function test_update_marca_pedido_como_entregue(): void
    {
        $consumidor = Consumidor::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        $response = $this->putJson("/entregas/{$pedido->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'estado' => 'ENTREGUE',
            ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'estado' => 'ENTREGUE',
        ]);
    }

    /**
     * Testa que update retorna a estrutura JSON correta.
     */
    public function test_update_retorna_estrutura_json_correta(): void
    {
        $consumidor = Consumidor::factory()->create();
        $pedido = Pedido::create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => 'REALIZADO',
        ]);

        $response = $this->putJson("/entregas/{$pedido->id}");

        $response->assertJsonStructure([
            'message',
            'data' => [
                'pedido_id',
                'estado',
            ],
        ]);
    }
}
