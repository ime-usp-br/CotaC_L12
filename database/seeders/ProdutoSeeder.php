<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

/**
 * Seeder para popular a tabela de produtos com os itens básicos.
 */
class ProdutoSeeder extends Seeder
{
    /**
     * Executa o seeder para o banco de dados.
     *
     * Cria os produtos básicos disponíveis para consumo,
     * como Café e Chá, com valor padrão de 1 unidade de cota.
     */
    public function run(): void
    {
        $produtos = [
            [
                'nome' => 'Café',
                'valor' => 1,
            ],
            [
                'nome' => 'Chá',
                'valor' => 1,
            ],
        ];

        foreach ($produtos as $produto) {
            Produto::firstOrCreate(
                ['nome' => $produto['nome']],
                ['valor' => $produto['valor']]
            );
        }
    }
}
