<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Entity;
use App\Models\ContactFunction;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        if (Entity::count() === 0) {
            $this->command->warn('Nenhuma entidade encontrada. Por favor, execute o EntitySeeder primeiro.');
            return;
        }

        if (ContactFunction::count() === 0) {
            $this->command->warn('Nenhuma função encontrada. Por favor, execute o ContactFunctionSeeder primeiro.');
            return;
        }


        Contact::factory()->count(11)->create();

        $this->command->info('11 contactos criados com sucesso!');
    }
}
