<?php

namespace App\Services;

use App\Models\Consumidor;
use App\Models\CotaRegular;
use Illuminate\Support\Facades\Log;

/**
 * Serviço responsável pelo cálculo de cota e saldo de consumidores.
 *
 * Implementa a lógica central do sistema para determinar quanto
 * crédito um consumidor possui disponível no mês corrente.
 */
class CotaService
{
    /**
     * Código da unidade IME-USP.
     */
    private const CODUND_IME = 8;

    /**
     * Cria uma nova instância do serviço.
     *
     * @param  ReplicadoService  $replicadoService  Serviço para consultar dados do Replicado.
     */
    public function __construct(
        private ReplicadoService $replicadoService
    ) {}

    /**
     * Calcula o saldo disponível para um consumidor no mês atual.
     *
     * Este método implementa a lógica central do sistema de cotas:
     * 1. Determina a cota mensal (especial > regular > zero)
     * 2. Calcula o gasto acumulado no mês atual
     * 3. Retorna o saldo (cota - gasto)
     *
     * @param  Consumidor  $consumidor  O consumidor a ser verificado.
     * @return array{cota: int, gasto: int, saldo: int} Array associativo com:
     *                                                  - cota: Valor da cota mensal do consumidor
     *                                                  - gasto: Total gasto no mês atual
     *                                                  - saldo: Saldo disponível (pode ser negativo)
     *
     * @throws \App\Exceptions\ReplicadoServiceException Se houver erro ao consultar o Replicado.
     */
    public function calcularSaldoParaConsumidor(Consumidor $consumidor): array
    {
        $cota = $this->obterCotaMensal($consumidor);
        $gasto = $this->calcularGastoMensal($consumidor);
        $saldo = $cota - $gasto;

        Log::info("CotaService: Calculated saldo for codpes {$consumidor->codpes}.", [
            'codpes' => $consumidor->codpes,
            'cota' => $cota,
            'gasto' => $gasto,
            'saldo' => $saldo,
        ]);

        return [
            'cota' => $cota,
            'gasto' => $gasto,
            'saldo' => $saldo,
        ];
    }

    /**
     * Determina a cota mensal de um consumidor.
     *
     * Segue a ordem de prioridade:
     * 1. Cota Especial (se existir)
     * 2. Maior Cota Regular entre os vínculos ativos do IME
     * 3. Zero (se não houver cotas aplicáveis)
     *
     * @param  Consumidor  $consumidor  O consumidor.
     * @return int Valor da cota mensal.
     *
     * @throws \App\Exceptions\ReplicadoServiceException Se houver erro ao consultar vínculos.
     */
    private function obterCotaMensal(Consumidor $consumidor): int
    {
        // 1. Verifica se existe Cota Especial
        $cotaEspecial = $consumidor->cotaEspecial;
        if ($cotaEspecial !== null) {
            $valor = $cotaEspecial->valor;
            Log::info("CotaService: Using special cota for codpes {$consumidor->codpes}: {$valor}");

            return $valor;
        }

        // 2. Busca vínculos ativos no IME
        $vinculos = $this->replicadoService->obterVinculosAtivos(
            $consumidor->codpes,
            self::CODUND_IME
        );

        if (empty($vinculos)) {
            Log::info("CotaService: No active vinculos found for codpes {$consumidor->codpes} in IME.");

            return 0;
        }

        // 3. Busca a maior cota regular entre os vínculos
        /** @var int|null $maiorCota */
        $maiorCota = CotaRegular::whereIn('vinculo', $vinculos)
            ->max('valor');

        if ($maiorCota === null) {
            Log::info("CotaService: No matching regular cota found for vinculos of codpes {$consumidor->codpes}.", [
                'vinculos' => $vinculos,
            ]);

            return 0;
        }

        Log::info("CotaService: Using regular cota for codpes {$consumidor->codpes}: ".(string) $maiorCota, [
            'vinculos' => $vinculos,
        ]);

        return $maiorCota;
    }

    /**
     * Calcula o total gasto pelo consumidor no mês atual.
     *
     * Soma o valor de todos os pedidos realizados no mês e ano correntes.
     * Valor do pedido = Σ (quantidade × produto.valor) para todos os itens.
     *
     * @param  Consumidor  $consumidor  O consumidor.
     * @return int Total gasto no mês atual.
     */
    private function calcularGastoMensal(Consumidor $consumidor): int
    {
        $mesAtual = now()->month;
        $anoAtual = now()->year;

        $gasto = $consumidor->pedidos()
            ->whereYear('created_at', $anoAtual)
            ->whereMonth('created_at', $mesAtual)
            ->with('itens')
            ->get()
            ->sum(function ($pedido) {
                return $pedido->itens->sum(function ($item) {
                    return $item->quantidade * $item->valor_unitario;
                });
            });

        return (int) $gasto;
    }
}
