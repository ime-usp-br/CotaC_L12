<?php

namespace Tests\Feature\Console;

use App\Models\Consumidor;
use App\Models\Pedido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpirarPedidosCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_expires_realizado_orders_older_than_one_hour()
    {
        $consumidor = Consumidor::factory()->create();
        $recent = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => Pedido::ESTADO_REALIZADO,
            'created_at' => now()->subMinutes(30),
        ]);
        $old = Pedido::factory()->create([
            'consumidor_codpes' => $consumidor->codpes,
            'estado' => Pedido::ESTADO_REALIZADO,
            'created_at' => now()->subMinutes(61),
        ]);

        $this->artisan('pedidos:expirar')
            ->assertSuccessful();

        $this->assertDatabaseHas('pedidos', [
            'id' => $old->id,
            'estado' => Pedido::ESTADO_EXPIRADO,
        ]);
        $this->assertDatabaseHas('pedidos', [
            'id' => $recent->id,
            'estado' => Pedido::ESTADO_REALIZADO,
        ]);
    }
}
