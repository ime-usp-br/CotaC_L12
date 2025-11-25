<?php

namespace App\Livewire;

use App\Models\Consumidor;
use App\Services\CotaService;
use App\Services\ReplicadoService;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Componente para buscar consumidor por Número USP.
 *
 * Responsável por validar o NUSP, buscar dados no Replicado
 * e calcular o saldo disponível para pedidos.
 */
class BuscaConsumidor extends Component
{
    /**
     * Número USP digitado pelo usuário.
     */
    public string $codpes = '';

    /**
     * Dados do consumidor obtidos do Replicado.
     *
     * @var array{codpes: int, nompes: string, emailusp: string}|null
     */
    public ?array $consumidorData = null;

    /**
     * Informações de cota, gasto e saldo.
     *
     * @var array{cota: int, gasto: int, saldo: int}|null
     */
    public ?array $saldoInfo = null;

    /**
     * Estado de validação do NUSP (null = não validado, true = válido, false = inválido).
     */
    public ?bool $nuspValid = null;

    /**
     * Valida o formato do NUSP on blur.
     */
    public function validateNusp(): void
    {
        // Não validar se o campo estiver vazio
        if (empty($this->codpes)) {
            $this->nuspValid = null;

            return;
        }

        // Validar se é um número inteiro
        if (is_numeric($this->codpes) && ctype_digit($this->codpes)) {
            $this->nuspValid = true;
        } else {
            $this->nuspValid = false;
        }
    }

    /**
     * Reseta a validação quando o usuário digita.
     */
    public function updatedCodpes(): void
    {
        $this->nuspValid = null;
    }

    /**
     * Busca o consumidor no Replicado e calcula o saldo.
     */
    public function buscar(): void
    {
        // Limpar estado anterior
        $this->reset(['consumidorData', 'saldoInfo']);

        // Validar entrada
        $this->validate([
            'codpes' => 'required|integer',
        ], [
            'codpes.required' => __('O Número USP é obrigatório.'),
            'codpes.integer' => __('O Número USP deve ser um número inteiro.'),
        ]);

        /** @var ReplicadoService $replicadoService */
        $replicadoService = app(ReplicadoService::class);

        /** @var CotaService $cotaService */
        $cotaService = app(CotaService::class);

        try {
            // Buscar pessoa no Replicado
            $pessoaData = $replicadoService->buscarPessoa((int) $this->codpes);

            if ($pessoaData === null) {
                $this->dispatch('toast', type: 'error', message: __('O Número USP informado não existe no sistema.'));

                return;
            }

            // Obter ou criar consumidor local
            $consumidor = Consumidor::firstOrCreate(
                ['codpes' => (int) $this->codpes],
                ['nome' => $pessoaData['nompes']]
            );

            // Calcular saldo
            $saldoInfo = $cotaService->calcularSaldoParaConsumidor($consumidor);

            // Atualizar estado
            $this->consumidorData = $pessoaData;
            $this->saldoInfo = $saldoInfo;

            // Emitir evento para outros componentes
            $this->dispatch('consumidorEncontrado', codpes: (int) $this->codpes, saldoInfo: $saldoInfo);

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: __('Erro ao buscar dados do consumidor. Tente novamente.'));
        }
    }

    /**
     * Limpa o formulário e reseta o estado.
     */
    public function limpar(): void
    {
        $this->reset(['codpes', 'consumidorData', 'saldoInfo']);
        $this->dispatch('consumidorLimpo');
    }

    /**
     * Escuta evento de pedido criado com sucesso.
     */
    #[On('pedidoCriado')]
    public function onPedidoCriado(): void
    {
        // Limpar todos os dados do consumidor mas NÃO disparar consumidorLimpo
        // pois isso limparia a mensagem de sucesso do CarrinhoPedido
        $this->reset(['codpes', 'consumidorData', 'saldoInfo']);
    }

    /**
     * Renderiza o componente.
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.busca-consumidor');
    }
}
