<?php

namespace App\Livewire;

use App\Models\Produto;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Componente para exibir produtos disponíveis.
 *
 * Permite ao usuário adicionar produtos ao carrinho
 * quando um consumidor válido estiver selecionado.
 */
class SelecaoProdutos extends Component
{
    /**
     * Produtos disponíveis no sistema.
     *
     * @var Collection<int, Produto>
     */
    public Collection $produtos;

    /**
     * Saldo disponível do consumidor atual.
     */
    public int $saldoDisponivel = 0;

    /**
     * Se a seleção de produtos está habilitada.
     */
    public bool $habilitado = false;

    /**
     * Inicializa o componente carregando produtos.
     */
    public function mount(): void
    {
        $this->produtos = Produto::all();
    }

    /**
     * Escuta evento de consumidor encontrado.
     *
     * @param  array{cota: int, gasto: int, saldo: int}  $saldoInfo
     */
    #[On('consumidorEncontrado')]
    public function onConsumidorEncontrado(int $codpes, array $saldoInfo): void
    {
        $this->saldoDisponivel = $saldoInfo['saldo'];
        $this->habilitado = true;
    }

    /**
     * Escuta evento de limpeza de consumidor.
     */
    #[On('consumidorLimpo')]
    public function onConsumidorLimpo(): void
    {
        $this->saldoDisponivel = 0;
        $this->habilitado = false;
    }

    /**
     * Escuta evento de pedido criado.
     */
    #[On('pedidoCriado')]
    public function onPedidoCriado(): void
    {
        $this->saldoDisponivel = 0;
        $this->habilitado = false;
    }

    /**
     * Adiciona produto ao carrinho.
     */
    public function adicionarAoCarrinho(int $produtoId): void
    {
        if (! $this->habilitado) {
            return;
        }

        $this->dispatch('produtoAdicionado', produtoId: $produtoId);
    }

    /**
     * Renderiza o componente.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.selecao-produtos');
    }
}
