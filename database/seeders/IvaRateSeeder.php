<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IvaRate;

class IvaRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $ivaRates = [
            [
                'nome' => 'Taxa Normal',
                'taxa' => 23.00,
                'descricao' => 'IVA à taxa normal de 23%',
                'estado' => 'ativo'
            ],
            [
                'nome' => 'Taxa Intermédia',
                'taxa' => 13.00,
                'descricao' => 'IVA à taxa intermédia de 13%',
                'estado' => 'ativo'
            ],
            [
                'nome' => 'Taxa Reduzida',
                'taxa' => 6.00,
                'descricao' => 'IVA à taxa reduzida de 6%',
                'estado' => 'ativo'
            ],
            [
                'nome' => 'Isento',
                'taxa' => 0.00,
                'descricao' => 'Isento de IVA',
                'estado' => 'ativo'
            ],
        ];

        foreach ($ivaRates as $rate) {
            IvaRate::firstOrCreate(
                ['taxa' => $rate['taxa']],
                $rate
            );
        }

        $this->command->info('Taxas de IVA criadas com sucesso!');

    }
}
