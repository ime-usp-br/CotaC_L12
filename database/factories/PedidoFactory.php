<?php

namespace Database\Factories;

use App\Models\Consumidor;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pedido>
 */
class PedidoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Pedido::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'consumidor_codpes' => Consumidor::factory(),
            'estado' => 'REALIZADO',
        ];
    }

    /**
     * Indicate that the pedido is delivered.
     */
    public function entregue(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'ENTREGUE',
        ]);
    }
}
