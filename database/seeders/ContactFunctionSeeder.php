<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactFunction;

class ContactFunctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $functions = [
            ['nome' => 'CEO', 'descricao' => 'Chief Executive Officer'],
            ['nome' => 'CFO', 'descricao' => 'Chief Financial Officer'],
            ['nome' => 'CTO', 'descricao' => 'Chief Technology Officer'],
            ['nome' => 'Diretor Comercial', 'descricao' => 'Responsável pela área comercial'],
            ['nome' => 'Diretor Financeiro', 'descricao' => 'Responsável pela área financeira'],
            ['nome' => 'Responsável de Compras', 'descricao' => 'Gestão de compras e fornecedores'],
            ['nome' => 'Responsável de Vendas', 'descricao' => 'Gestão de vendas e clientes'],
            ['nome' => 'Gestor de Projetos', 'descricao' => 'Coordenação de projetos'],
            ['nome' => 'Técnico de Suporte', 'descricao' => 'Suporte técnico'],
            ['nome' => 'Administrativo', 'descricao' => 'Funções administrativas'],
            ['nome' => 'Assistente Administrativo', 'descricao' => 'Apoio administrativo'],
            ['nome' => 'Coordenador', 'descricao' => 'Coordenação de equipa'],
            ['nome' => 'Técnico', 'descricao' => 'Funções técnicas'],
            ['nome' => 'Operador', 'descricao' => 'Operador geral da primeira linha']
        ];

        foreach ($functions as $function) {
            ContactFunction::firstOrCreate(
                [
                    'nome' => $function['nome']
                ],
                [
                    'descricao' => $function['descricao'],
                    'estado' => 'ativo'
                ]
            );
        }
    }

}
