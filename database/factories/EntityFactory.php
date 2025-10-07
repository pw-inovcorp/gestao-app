<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entity>
 */
class EntityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isCliente = fake()->boolean(70);
        $isFornecedor = fake()->boolean(50);

        return [
            'nif' => fake()->unique()->numerify('#########'),
            'nome' => fake()->company(),
            'morada' => fake()->streetAddress(),
            'codigo_postal' => fake()->numerify('####-###'),
            'localidade' => fake()->city(),
            'country_id' => Country::inRandomOrder()->first()?->id ?? null,
            'telefone' => fake()->optional(0.7)->numerify('2########'),
            'telemovel' => fake()->optional(0.9)->numerify('9########'),
            'website' => fake()->optional(0.5)->url(),
            'email' => fake()->optional(0.8)->companyEmail(),
            'consentimento_rgpd' => fake()->boolean(80),
            'observacoes' => fake()->optional(0.3)->sentence(),
            'is_cliente' => $isCliente,
            'is_fornecedor' => $isFornecedor,
            'estado' => fake()->randomElement(['ativo', 'ativo', 'ativo', 'inativo'])
        ];
    }


    public function cliente(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cliente' => true,
            'is_fornecedor' => false,
        ]);
    }

    public function fornecedor(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cliente' => false,
            'is_fornecedor' => true,
        ]);
    }


    public function ambos(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cliente' => true,
            'is_fornecedor' => true,
        ]);
    }

    public function ativo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'ativo',
        ]);
    }

    public function inativo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inativo',
        ]);
    }

}
