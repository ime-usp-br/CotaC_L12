<?php

namespace App\Services;

use App\Exceptions\ReplicadoServiceException; // Import custom exception
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Uspdev\Replicado\Pessoa;

/**
 * Classe de serviço para interagir com o banco de dados Replicado da USP.
 */
class ReplicadoService
{
    /**
     * Busca dados básicos de uma pessoa no Replicado.
     *
     * @param  int  $codpes  O Número USP (NUSP).
     * @return array{codpes: int, nompes: string, emailusp: string}|null Retorna um array com os dados da pessoa ou null se não encontrada.
     *
     * @throws \App\Exceptions\ReplicadoServiceException Se ocorrer um problema de comunicação com o banco de dados Replicado.
     */
    public function buscarPessoa(int $codpes): ?array
    {
        $cacheKey = "replicado.pessoa.{$codpes}";

        /** @var array{codpes: int, nompes: string, emailusp: string}|null */
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($codpes) {
            try {
                $pessoa = Pessoa::dump($codpes);

                if (empty($pessoa) || ! isset($pessoa['nompes'])) {
                    Log::info("Replicado: Person not found for codpes {$codpes}.");

                    return null;
                }

                $emailUsp = Pessoa::retornarEmailUsp($codpes);

                return [
                    'codpes' => $codpes,
                    'nompes' => is_string($pessoa['nompes']) ? $pessoa['nompes'] : '',
                    'emailusp' => $emailUsp ?: '',
                ];
            } catch (\Exception $e) {
                Log::error("Replicado Service Error: Failed fetching person data for codpes {$codpes}. Error: ".$e->getMessage(), ['exception' => $e]);
                throw new ReplicadoServiceException('Replicado service communication error while fetching person data.', 0, $e);
            }
        });
    }

    /**
     * Obtém os vínculos ativos de uma pessoa em uma unidade específica.
     *
     * Retorna apenas vínculos ativos (sitatl = 'A' ou 'P') da unidade especificada.
     *
     * @param  int  $codpes  O Número USP (NUSP).
     * @param  int  $codund  O código da unidade (ex: 8 para IME).
     * @return array<int, string> Array com as siglas dos vínculos ativos (ex: ['SERVIDOR', 'ALUNOPOS']).
     *
     * @throws \App\Exceptions\ReplicadoServiceException Se ocorrer um problema de comunicação com o banco de dados Replicado.
     */
    public function obterVinculosAtivos(int $codpes, int $codund): array
    {
        $cacheKey = "replicado.vinculos.{$codpes}.{$codund}";

        /** @var array<int, string> */
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($codpes, $codund) {
            try {
                // Configura temporariamente o código da unidade no ambiente
                $originalCodeUnd = getenv('REPLICADO_CODUNDCLG');
                putenv("REPLICADO_CODUNDCLG={$codund}");

                $vinculos = Pessoa::obterSiglasVinculosAtivos($codpes);

                // Restaura o valor original
                if ($originalCodeUnd !== false) {
                    putenv("REPLICADO_CODUNDCLG={$originalCodeUnd}");
                } else {
                    putenv('REPLICADO_CODUNDCLG');
                }

                if (empty($vinculos)) {
                    Log::info("Replicado: No active vinculos found for codpes {$codpes} in unit {$codund}.");

                    return [];
                }

                /** @var array<int, string> */
                return $vinculos;
            } catch (\Exception $e) {
                Log::error("Replicado Service Error: Failed fetching vinculos for codpes {$codpes} in unit {$codund}. Error: ".$e->getMessage(), ['exception' => $e]);
                throw new ReplicadoServiceException('Replicado service communication error while fetching vinculos.', 0, $e);
            }
        });
    }

    /**
     * Valida se o Número USP (codpes) e o e-mail fornecidos pertencem à mesma pessoa válida no Replicado.
     *
     * Este método consulta o Replicado para verificar a existência do `codpes`
     * e se o `email` fornecido está associado a esse `codpes`.
     *
     * @param  int  $codpes  O Número USP (NUSP).
     * @param  string  $email  O endereço de e-mail para validar em conjunto com o `codpes`.
     * @return bool Retorna `true` se o `codpes` e o `email` corresponderem a uma pessoa válida, `false` caso contrário.
     *
     * @throws \App\Exceptions\ReplicadoServiceException Se ocorrer um problema de comunicação com o banco de dados Replicado.
     */
    public function validarNuspEmail(int $codpes, string $email): bool
    {
        if (! str_ends_with(strtolower($email), 'usp.br')) {
            Log::warning("Replicado Validation: Attempt to validate non-USP email '{$email}' for codpes {$codpes}.");
            // Depending on strictness, this might be an early return false or even an exception.
            // For now, let it proceed to check against Replicado records.
        }

        try {
            $emailsPessoa = Pessoa::emails($codpes);

            if (empty($emailsPessoa)) {
                Log::info("Replicado Validation: No person found or no emails registered for codpes {$codpes}.");

                return false;
            }

            foreach ($emailsPessoa as $emailCadastrado) {
                if (is_string($emailCadastrado) && (strtolower(trim($emailCadastrado)) === strtolower($email))) {
                    Log::info("Replicado Validation: Success for codpes {$codpes} and email '{$email}'.");

                    return true;
                }
            }

            Log::info("Replicado Validation: Email '{$email}' does not match registered emails for codpes {$codpes}.");

            return false;

        } catch (\Exception $e) {
            Log::error("Replicado Service Error: Failed validating codpes {$codpes} and email '{$email}'. Error: ".$e->getMessage(), ['exception' => $e]);
            // Re-throw as a custom, more specific exception for better handling by callers.
            throw new ReplicadoServiceException('Replicado service communication error while validating NUSP/email.', 0, $e);
        }
    }
}
