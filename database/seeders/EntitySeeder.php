<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {

        Entity::factory()->count(2)->cliente()->ativo()->create();


        Entity::factory()->count(2)->fornecedor()->ativo()->create();


        Entity::factory()->count(1)->ambos()->ativo()->create();
    }
}
