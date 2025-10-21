<?php

namespace Tests\Fakes;

use App\Exceptions\ReplicadoServiceException; // Import custom exception
use App\Services\ReplicadoService;

/**
 * Fake implementation of ReplicadoService for testing purposes.
 */
class FakeReplicadoService extends ReplicadoService
{
    /**
     * Controls the return value of validarNuspEmail.
     * true = success, false = validation failure.
     */
    public ?bool $validationResult = true;

    /**
     * Determines if the service should throw an exception.
     */
    public bool $shouldThrowException = false;

    /**
     * Storage for fake pessoa data.
     */
    private array $pessoas = [];

    /**
     * Storage for fake vinculos data.
     */
    private array $vinculos = [];

    /**
     * Simulates the validation logic.
     *
     *
     * @throws \App\Exceptions\ReplicadoServiceException
     */
    public function validarNuspEmail(int $codpes, string $email): bool
    {
        if ($this->shouldThrowException) {
            // Simulate a service communication error by throwing the custom exception
            throw new ReplicadoServiceException('Simulated Replicado service communication error.');
        }

        // Return the predefined result (true for success, false for invalid)
        // Default to true if not set explicitly, or handle as per specific test needs.
        return $this->validationResult ?? true;
    }

    /**
     * Sets the desired validation result for the next call.
     *
     * @param  bool|null  $result  true for success, false for failure.
     * @return $this
     */
    public function shouldReturn(?bool $result): self
    {
        $this->validationResult = $result;
        $this->shouldThrowException = false; // Ensure it doesn't throw an exception when a return value is set.

        return $this;
    }

    /**
     * Configures the fake service to throw a ReplicadoServiceException on the next call.
     *
     * @return $this
     */
    public function shouldFail(): self
    {
        $this->shouldThrowException = true;

        return $this;
    }

    /**
     * Sets fake pessoa data for a specific codpes.
     *
     * @param  array  $data  Array with keys: 'nompes', 'emailusp'
     * @return $this
     */
    public function setPessoa(int $codpes, array $data): self
    {
        $this->pessoas[$codpes] = $data;

        return $this;
    }

    /**
     * Sets fake vinculos data for a specific codpes and unit.
     *
     * @param  array  $vinculos  Array of vinculos siglas (e.g., ['SERVIDOR', 'ALUNOPOS'])
     * @return $this
     */
    public function setVinculos(int $codpes, int $codund, array $vinculos): self
    {
        $this->vinculos["{$codpes}_{$codund}"] = $vinculos;

        return $this;
    }

    /**
     * Simulates buscarPessoa method.
     *
     *
     * @throws \App\Exceptions\ReplicadoServiceException
     */
    public function buscarPessoa(int $codpes): ?array
    {
        if ($this->shouldThrowException) {
            throw new ReplicadoServiceException('Simulated Replicado service communication error.');
        }

        if (! isset($this->pessoas[$codpes])) {
            return null;
        }

        return [
            'codpes' => $codpes,
            'nompes' => $this->pessoas[$codpes]['nompes'] ?? 'Test Person',
            'emailusp' => $this->pessoas[$codpes]['emailusp'] ?? 'test@usp.br',
        ];
    }

    /**
     * Simulates obterVinculosAtivos method.
     *
     *
     * @throws \App\Exceptions\ReplicadoServiceException
     */
    public function obterVinculosAtivos(int $codpes, int $codund): array
    {
        if ($this->shouldThrowException) {
            throw new ReplicadoServiceException('Simulated Replicado service communication error.');
        }

        $key = "{$codpes}_{$codund}";

        return $this->vinculos[$key] ?? [];
    }
}
