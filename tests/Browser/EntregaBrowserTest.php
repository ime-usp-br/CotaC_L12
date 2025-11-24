<?php

namespace Tests\Browser;

use App\Models\Consumidor;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EntregaBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_lista_pedidos_pendentes_e_marca_como_entregue()
    {
        $this->browse(function (Browser $browser) {
            // Arrange
            $consumidor = Consumidor::factory()->create(['nome' => 'Consumidor Teste Browser']);
            $produto = Produto::factory()->create(['nome' => 'Café Expresso Browser']);

            $pedido = Pedido::factory()->create([
                'consumidor_codpes' => $consumidor->codpes,
                'estado' => 'REALIZADO',
            ]);
            $pedido->itens()->create([
                'produto_id' => $produto->id,
                'quantidade' => 1,
            ]);

            // Act & Assert
            $browser->visit('/entregas/pendentes')
                ->waitForText('Pedidos Pendentes')
                ->assertSee('Consumidor Teste Browser')
                ->assertSee('Café Expresso Browser')
                ->assertSee('Pedido #'.$pedido->id)
                ->press('Marcar como Entregue')
                ->waitUntilMissingText('Consumidor Teste Browser')
                ->assertDontSee('Consumidor Teste Browser');
        });
    }

    public function test_atualizacao_automatica_lista()
    {
        $this->browse(function (Browser $browser) {
            // Arrange: Começa na página
            $browser->visit('/entregas/pendentes');

            // Act: Cria um pedido no banco de dados (simulando outro processo)
            $consumidor = Consumidor::factory()->create(['nome' => 'Novo Consumidor Auto']);
            $produto = Produto::factory()->create(['nome' => 'Bolo Auto']);

            $pedido = Pedido::factory()->create([
                'consumidor_codpes' => $consumidor->codpes,
                'estado' => 'REALIZADO',
            ]);
            $pedido->itens()->create([
                'produto_id' => $produto->id,
                'quantidade' => 1,
            ]);

            // Assert: Espera o Livewire poll atualizar a tela
            $browser->waitForText('Novo Consumidor Auto', 10) // Espera até 10s (poll é 5s)
                ->assertSee('Bolo Auto');
        });
    }
}
