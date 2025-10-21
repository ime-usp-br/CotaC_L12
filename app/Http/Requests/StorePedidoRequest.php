<?php

namespace App\Http\Requests;

use App\Models\Consumidor;
use App\Models\Produto;
use App\Services\CotaService;
use App\Services\ReplicadoService;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request para validação de criação de pedido.
 *
 * Valida os dados de entrada e garante que o consumidor
 * possui saldo suficiente para realizar o pedido.
 */
class StorePedidoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        // Endpoint público (balcão), não requer autenticação
        return true;
    }

    /**
     * Regras de validação para a requisição.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'codpes' => ['required', 'integer'],
            'produtos' => ['required', 'array', 'min:1'],
            'produtos.*.id' => ['required', 'integer', 'exists:produtos,id'],
            'produtos.*.quantidade' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Mensagens de validação customizadas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'codpes.required' => __('O Número USP é obrigatório.'),
            'codpes.integer' => __('O Número USP deve ser um número inteiro.'),
            'produtos.required' => __('Selecione ao menos um produto.'),
            'produtos.min' => __('Selecione ao menos um produto.'),
            'produtos.*.id.required' => __('O produto é obrigatório.'),
            'produtos.*.id.exists' => __('Produto selecionado não existe.'),
            'produtos.*.quantidade.required' => __('A quantidade é obrigatória.'),
            'produtos.*.quantidade.integer' => __('A quantidade deve ser um número inteiro.'),
            'produtos.*.quantidade.min' => __('A quantidade deve ser pelo menos 1.'),
        ];
    }

    /**
     * Configuração de validação adicional após as regras padrão.
     *
     * Valida se o consumidor existe no Replicado e possui saldo suficiente.
     *
     * @return array<int, callable(ValidatorContract): void>
     */
    public function after(): array
    {
        return [
            function (ValidatorContract $validator) {
                /** @var int $codpes */
                $codpes = $this->input('codpes');

                // Validar se codpes existe no Replicado
                /** @var ReplicadoService $replicadoService */
                $replicadoService = app(ReplicadoService::class);
                $pessoaData = $replicadoService->buscarPessoa($codpes);

                if ($pessoaData === null) {
                    $validator->errors()->add(
                        'codpes',
                        __('O Número USP informado não existe no sistema.')
                    );

                    return; // Parar validação se codpes inválido
                }

                // Obter ou criar consumidor
                $consumidor = Consumidor::firstOrNew(['codpes' => $codpes]);

                // Calcular valor total do pedido
                /** @var array<int, array{id: int, quantidade: int}> $produtos */
                $produtos = $this->input('produtos', []);
                $valorTotal = $this->calcularValorTotal($produtos);

                // Obter saldo do consumidor
                /** @var CotaService $cotaService */
                $cotaService = app(CotaService::class);
                $saldoInfo = $cotaService->calcularSaldoParaConsumidor($consumidor);

                // Validar se há saldo suficiente
                if ($valorTotal > $saldoInfo['saldo']) {
                    $validator->errors()->add(
                        'saldo',
                        __(
                            'Saldo insuficiente. Disponível: :saldo, Necessário: :total',
                            [
                                'saldo' => $saldoInfo['saldo'],
                                'total' => $valorTotal,
                            ]
                        )
                    );
                }
            },
        ];
    }

    /**
     * Calcula o valor total do pedido baseado nos produtos e quantidades.
     *
     * @param  array<int, array{id: int, quantidade: int}>  $produtos  Array de produtos.
     * @return int Valor total do pedido.
     */
    private function calcularValorTotal(array $produtos): int
    {
        $total = 0;

        foreach ($produtos as $produto) {
            $produtoModel = Produto::find($produto['id']);
            if ($produtoModel) {
                $total += $produtoModel->valor * $produto['quantidade'];
            }
        }

        return $total;
    }
}
