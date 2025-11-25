<?php

namespace App\Livewire;

use App\Models\Produto;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Componente de carrinho de pedidos.
 *
 * Gerencia itens do carrinho, calcula totais e submete pedidos.
 */
class CarrinhoPedido extends Component
{
    /**
     * Itens do carrinho [produto_id => quantidade].
     *
     * @var array<int, int>
     */
    public array $itens = [];

    /**
     * Número USP do consumidor atual.
     */
    public int $codpes = 0;

    /**
     * Saldo disponível do consumidor.
     */
    public int $saldoDisponivel = 0;

    /**
     * Estado de processamento.
     */
    public bool $processando = false;

    /**
     * Escuta evento de consumidor encontrado.
     *
     * @param  array{cota: int, gasto: int, saldo: int}  $saldoInfo
     */
    #[On('consumidorEncontrado')]
    public function onConsumidorEncontrado(int $codpes, array $saldoInfo): void
    {
        $this->codpes = $codpes;
        $this->saldoDisponivel = $saldoInfo['saldo'];
        $this->reset(['itens']);
    }

    /**
     * Escuta evento de produto adicionado.
     */
    #[On('produtoAdicionado')]
    public function adicionarProduto(int $produtoId): void
    {
        if (isset($this->itens[$produtoId])) {
            $this->itens[$produtoId]++;
        } else {
            $this->itens[$produtoId] = 1;
        }
    }

    /**
     * Escuta evento de limpeza de consumidor.
     */
    #[On('consumidorLimpo')]
    public function limparCarrinho(): void
    {
        $this->reset(['itens', 'codpes', 'saldoDisponivel']);
    }

    /**
     * Remove produto do carrinho.
     */
    public function removerProduto(int $produtoId): void
    {
        unset($this->itens[$produtoId]);
    }

    /**
     * Calcula o total do carrinho.
     */
    public function calcularTotal(): int
    {
        $total = 0;

        foreach ($this->itens as $produtoId => $quantidade) {
            $produto = Produto::find($produtoId);
            if ($produto) {
                $total += $produto->valor * $quantidade;
            }
        }

        return $total;
    }

    /**
     * Finaliza o pedido submetendo ao backend.
     */
    public function finalizarPedido(): void
    {
        // Validações
        if (empty($this->itens)) {
            $this->dispatch('toast', type: 'error', message: __('Adicione ao menos um produto ao carrinho.'));

            return;
        }

        $total = $this->calcularTotal();

        if ($total > $this->saldoDisponivel) {
            $this->dispatch('toast', type: 'error', message: __('Saldo insuficiente. Disponível: :saldo, Necessário: :total', [
                'saldo' => $this->saldoDisponivel,
                'total' => $total,
            ]));

            return;
        }

        $this->processando = true;

        try {
            // Preparar dados do pedido
            $produtos = [];
            foreach ($this->itens as $produtoId => $quantidade) {
                $produtos[] = [
                    'id' => $produtoId,
                    'quantidade' => $quantidade,
                ];
            }

            // Criar request diretamente usando o FormRequest validation
            $request = new \App\Http\Requests\StorePedidoRequest;
            $request->replace([
                'codpes' => $this->codpes,
                'produtos' => $produtos,
            ]);

            // Validar request
            $validator = \Illuminate\Support\Facades\Validator::make(
                $request->all(),
                $request->rules(),
                $request->messages()
            );

            if ($validator->fails()) {
                $errors = $validator->errors();
                $errorMessage = $errors->first('saldo') ?: $errors->first() ?: __('Erro na validação do pedido.');
                $this->dispatch('toast', type: 'error', message: is_string($errorMessage) ? $errorMessage : __('Erro na validação do pedido.'));

                return;
            }

            // Chamar o controller/service para criar o pedido
            /** @var \App\Services\PedidoService $pedidoService */
            $pedidoService = app(\App\Services\PedidoService::class);

            /** @var \App\Models\Consumidor $consumidor */
            $consumidor = \App\Models\Consumidor::where('codpes', $this->codpes)->firstOrFail();

            $pedido = $pedidoService->criarPedido($consumidor, $produtos);

            // Disparar evento para o Alpine.js no topo da página
            $this->dispatch('pedido-criado', pedidoId: $pedido->id);

            // Mostrar toast de sucesso
            $this->dispatch('toast', type: 'success', message: __('Pedido criado com sucesso!'));

            // Limpar carrinho
            $this->reset(['itens']);

            // Notificar BuscaConsumidor para limpar dados do consumidor
            $this->dispatch('pedidoCriado');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = $e->validator->errors()->first('saldo') ?: __('Erro ao criar pedido. Tente novamente.');
            $this->dispatch('toast', type: 'error', message: is_string($errorMessage) ? $errorMessage : __('Erro ao criar pedido. Tente novamente.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao criar pedido: '.$e->getMessage(), [
                'codpes' => $this->codpes,
                'produtos' => $this->itens,
                'exception' => $e,
            ]);
            $this->dispatch('toast', type: 'error', message: __('Erro ao processar pedido. Tente novamente.'));
        } finally {
            $this->processando = false;
        }
    }

    /**
     * Renderiza o componente.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.carrinho-pedido', [
            'produtos' => Produto::whereIn('id', array_keys($this->itens))->get(),
            'total' => $this->calcularTotal(),
            'saldoSuficiente' => $this->calcularTotal() <= $this->saldoDisponivel,
        ]);
    }
}
