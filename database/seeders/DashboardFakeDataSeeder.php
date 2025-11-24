<?php

namespace Database\Seeders;

use App\Models\Consumidor;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DashboardFakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have products
        if (Produto::count() === 0) {
            $this->command->info('Creating fake products...');
            Produto::factory(10)->create();
        }

        $produtos = Produto::all();

        // Create Consumers with Categories
        $categories = ['Docente', 'Servidor', 'Aluno'];
        $consumers = [];

        $this->command->info('Creating fake consumers...');
        foreach ($categories as $category) {
            $consumers[$category] = Consumidor::factory(5)->create([
                'categoria' => $category,
            ]);
        }

        // Create Orders for the last 30 days
        $this->command->info('Creating fake orders for the last 30 days...');

        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Random number of orders per day (0 to 10)
            $ordersCount = rand(0, 10);

            for ($i = 0; $i < $ordersCount; $i++) {
                $category = $categories[array_rand($categories)];
                $consumer = $consumers[$category]->random();

                $pedido = Pedido::create([
                    'consumidor_codpes' => $consumer->codpes,
                    'estado' => 'REALIZADO',
                    'created_at' => $date->copy()->setTime(rand(8, 18), rand(0, 59)),
                    'updated_at' => $date->copy()->setTime(rand(8, 18), rand(0, 59)),
                ]);

                // Add items to order
                $itemsCount = rand(1, 5);
                for ($j = 0; $j < $itemsCount; $j++) {
                    $produto = $produtos->random();
                    ItemPedido::create([
                        'pedido_id' => $pedido->id,
                        'produto_id' => $produto->id,
                        'quantidade' => rand(1, 3),
                        'valor_unitario' => $produto->valor,
                    ]);
                }
            }
        }

        $this->command->info('Dashboard fake data seeded successfully!');
    }
}
