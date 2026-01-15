<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nome' => 'Eventos',
                'descricao' => 'Eventos gerais, encontros e produções diversas',
            ],
            [
                'nome' => 'Convenções',
                'descricao' => 'Convenções, feiras, exposições e encontros temáticos',
            ],
            [
                'nome' => 'Teatro e Musicais',
                'descricao' => 'Peças teatrais, musicais e espetáculos cênicos',
            ],
            [
                'nome' => 'Ensaios',
                'descricao' => 'Ensaios fotográficos, artísticos ou promocionais',
            ],
            [
                'nome' => 'Dança',
                'descricao' => 'Apresentações, workshops e eventos de dança',
            ],
            [
                'nome' => 'Shows e Concertos',
                'descricao' => 'Shows ao vivo, concertos e apresentações musicais',
            ],
            [
                'nome' => 'E-commerce',
                'descricao' => 'Lojas virtuais, lançamentos de produtos e vendas online',
            ],
            [
                'nome' => 'Workshops e Cursos',
                'descricao' => 'Aulas, oficinas, cursos e treinamentos',
            ],
        ];

        foreach ($categorias as $categoria) {
            DB::table('categorias')->updateOrInsert(
                ['nome' => $categoria['nome']],
                [
                    'descricao' => $categoria['descricao'],
                    'atualizado_em' => now(),
                    'criado_em' => now(),
                ]
            );
        }
    }
}
