<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        CompanySetting::firstOrCreate(
            ['id' => 1],
            [
                'nome' => 'Nome da Empresa',
                'morada' => null,
                'codigo_postal' => null,
                'localidade' => null,
                'numero_contribuinte' => null,
                'logo' => null,
            ]
        );
    }
}
