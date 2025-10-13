<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $produtos = [
            'Computador Portátil',
            'Monitor LED 24"',
            'Teclado Mecânico',
            'Rato Wireless',
            'Webcam HD',
            'Headset USB',
            'Impressora Laser',
            'Scanner Documentos',
            'Disco Externo 1TB',
            'Pen Drive 64GB',
            'Router Wi-Fi',
            'Switch 8 Portas',
            'Cabo HDMI 2m',
            'Adaptador USB-C',
            'Hub USB 4 Portas',
        ];

        return [
            'nome' => fake()->randomElement($produtos),
            'descricao' => fake()->optional(0.9)->sentence(10),
            'preco' => fake()->randomFloat(2, 10, 999),
            'iva_rate_id' => \App\Models\IvaRate::inRandomOrder()->first()->id,
            'foto' => null,
            'observacoes' => fake()->optional(0.5)->sentence(),
            'estado' => fake()->randomElement(['ativo', 'ativo', 'ativo', 'inativo']),
        ];
    }
}
