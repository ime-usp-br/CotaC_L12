<?php

namespace Database\Factories;

use App\Models\Consumidor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consumidor>
 */
class ConsumidorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Consumidor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'codpes' => fake()->unique()->numberBetween(100000, 999999),
            'nome' => fake()->name(),
        ];
    }
}
