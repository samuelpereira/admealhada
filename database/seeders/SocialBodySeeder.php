<?php

namespace Database\Seeders;

use App\Models\SocialBody;
use Illuminate\Database\Seeder;

class SocialBodySeeder extends Seeder
{
    public function run(): void
    {
        $membros = [
            // Direção
            ['categoria' => 'direcao', 'cargo' => 'Presidente', 'nome' => 'Gonçalo Dias', 'ordem' => 1],
            ['categoria' => 'direcao', 'cargo' => 'Vice-Presidente', 'nome' => 'António Cardoso', 'ordem' => 2],
            ['categoria' => 'direcao', 'cargo' => 'Vice-Presidente', 'nome' => 'Samuel Pereira', 'ordem' => 3],
            ['categoria' => 'direcao', 'cargo' => 'Tesoureiro', 'nome' => 'Catarina Tovim', 'ordem' => 4],
            ['categoria' => 'direcao', 'cargo' => 'Secretário', 'nome' => 'Cíntia Barbosa', 'ordem' => 5],
            ['categoria' => 'direcao', 'cargo' => '1º Vogal', 'nome' => 'Joaquim Oliveira', 'ordem' => 6],
            ['categoria' => 'direcao', 'cargo' => '2º Vogal', 'nome' => 'António Gonçalves', 'ordem' => 7],

            // Assembleia Geral
            ['categoria' => 'assembleia_geral', 'cargo' => 'Presidente', 'nome' => 'Rui Barbosa', 'ordem' => 1],
            ['categoria' => 'assembleia_geral', 'cargo' => '1º Secretário', 'nome' => 'Pedro Figueiredo', 'ordem' => 2],
            ['categoria' => 'assembleia_geral', 'cargo' => '2º Secretário', 'nome' => 'João Oliveira', 'ordem' => 3],

            // Conselho Fiscal
            ['categoria' => 'conselho_fiscal', 'cargo' => 'Presidente', 'nome' => 'Catarina Cruz', 'ordem' => 1],
            ['categoria' => 'conselho_fiscal', 'cargo' => '1º Vogal', 'nome' => 'Carla Queiroz', 'ordem' => 2],
            ['categoria' => 'conselho_fiscal', 'cargo' => '2º Vogal', 'nome' => 'Isabel Cruz', 'ordem' => 3],
        ];

        foreach ($membros as $membro) {
            SocialBody::updateOrCreate(
                [
                    'categoria' => $membro['categoria'],
                    'cargo' => $membro['cargo'],
                    'nome' => $membro['nome'],
                ],
                [
                    'ordem' => $membro['ordem'],
                    'ativo' => true,
                ],
            );
        }
    }
}
