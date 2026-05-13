<?php

namespace Tests\Browser;

use App\Models\Consumidor;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class EntregaFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    #[Group('dusk')]
    #[Group('entrega')]
    public function test_atendente_pode_marcar_pedido_como_entregue(): void
    {
        $this->browse(function (Browser $browser) {
            $consumidor = Consumidor::factory()->create(['nome' => 'Consumidor Entrega']);
            $produto = Produto::factory()->create(['nome' => 'Bolo Entrega']);

            $pedido = Pedido::factory()->create([
                'consumidor_codpes' => $consumidor->codpes,
                'estado' => 'REALIZADO',
            ]);
            $pedido->itens()->create([
                'produto_id' => $produto->id,
                'quantidade' => 1,
                'valor_unitario' => $produto->valor,
            ]);

            $browser->visit('/entregas/pendentes')
                ->waitForText('Pedidos Pendentes')
                ->assertSee('Consumidor Entrega')
                ->assertSee('Bolo Entrega')
                ->assertSee('Pedido #'.$pedido->id)
                ->click('@marcar-entregue-'.$pedido->id)
                ->waitUntilMissingText('Consumidor Entrega')
                ->assertDontSee('Consumidor Entrega');
        });
    }
}
