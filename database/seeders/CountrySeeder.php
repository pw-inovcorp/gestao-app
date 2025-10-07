<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {


        $countries = json_decode(file_get_contents(database_path('seeders/data/countries.json')), true);

        foreach ($countries as $code => $name) {
            Country::firstOrCreate(
                ['codigo' => $code],
                ['nome' => $name, 'estado' => 'ativo']
            );
        }
    }
}
