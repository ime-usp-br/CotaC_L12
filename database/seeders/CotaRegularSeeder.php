<?php

namespace Database\Seeders;

use App\Models\CotaRegular;
use Illuminate\Database\Seeder;

/**
 * Seeder para popular a tabela de cotas regulares.
 */
class CotaRegularSeeder extends Seeder
{
    /**
     * Executa o seeder para o banco de dados.
     *
     * Cria as cotas mensais padrão baseadas nos vínculos USP.
     * Os valores foram definidos com base na análise do sistema legado
     * e na hierarquia acadêmica da instituição.
     */
    public function run(): void
    {
        $cotas = [
            [
                'vinculo' => 'DOCENTE',
                'valor' => 20,
            ],
            [
                'vinculo' => 'SERVIDOR',
                'valor' => 15,
            ],
            [
                'vinculo' => 'ALUNOPOS',
                'valor' => 10,
            ],
            [
                'vinculo' => 'ALUNOGR',
                'valor' => 5,
            ],
            [
                'vinculo' => 'ESTAGIARIO',
                'valor' => 5,
            ],
        ];

        foreach ($cotas as $cota) {
            CotaRegular::firstOrCreate(
                ['vinculo' => $cota['vinculo']],
                ['valor' => $cota['valor']]
            );
        }
    }
}
