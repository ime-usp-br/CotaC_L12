<?php

namespace Tests\Browser;

use App\Models\CotaRegular;
use App\Models\Produto;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Http;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class PedidoFlowTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $codpes = 123456;

        Http::post(
            rtrim(config('app.url'), '/').'/__testing/setup-replicado',
            [
                'pessoas' => [
                    $codpes => ['nompes' => 'Prof Teste', 'emailusp' => 'prof@usp.br'],
                ],
                'vinculos' => [
                    "{$codpes}_8" => ['DOCENTE'],
                ],
            ]
        );
    }

    #[Test]
    #[Group('dusk')]
    #[Group('pedido')]
    public function test_usuario_pode_fazer_pedido_com_sucesso(): void
    {
        $codpes = 123456;
        $produto = Produto::factory()->create(['nome' => 'Café Teste', 'valor' => 5]);
        CotaRegular::create(['vinculo' => 'DOCENTE', 'valor' => 100]);

        $this->browse(function (Browser $browser) use ($codpes, $produto) {
            $browser->visit('/pedidos')
                ->waitFor('@codpes-input')
                ->type('@codpes-input', (string) $codpes)
                ->click('@buscar-button')
                ->waitForText('Saldo Disponível')
                ->assertSee('100')
                ->click('@adicionar-produto-'.$produto->id)
                ->waitFor('@finalizar-pedido-button')
                ->click('@finalizar-pedido-button')
                ->waitFor('@pedido-sucesso-message', 5)
                ->assertVisible('@pedido-sucesso-message')
                ->assertSee('Pedido #')
                ->assertSee('criado com sucesso');
        });
    }
}
