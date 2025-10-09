<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Entity;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entity_id' => Entity::inRandomOrder()->first()?->id ?? Entity::factory(),
            'nome' => fake()->firstName(),
            'apelido' => fake()->lastName(),
            'contact_function_id' => \App\Models\ContactFunction::inRandomOrder()->first()?->id,
            'telefone' => fake()->optional(0.6)->numerify('2########'),
            'telemovel' => fake()->optional(0.9)->numerify('9########'),
            'email' => fake()->optional(0.8)->safeEmail(),
            'consentimento_rgpd' => fake()->boolean(75),
            'observacoes' => fake()->optional(0.3)->sentence(),
            'estado' => fake()->randomElement(['ativo', 'ativo', 'ativo', 'inativo']),
        ];
    }
}
