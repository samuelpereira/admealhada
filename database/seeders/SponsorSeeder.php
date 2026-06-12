<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $patrocinadores = [
            [
                'nome' => 'Mealhada Sport',
                'link' => 'https://example.com/mealhada-sport',
                'ordem' => 1,
            ],
            [
                'nome' => 'Basquetebol Aveiro',
                'link' => 'https://example.com/basquetebol-aveiro',
                'ordem' => 2,
            ],
            [
                'nome' => 'Pavilhão Mealhada',
                'link' => 'https://example.com/pavilhao-mealhada',
                'ordem' => 3,
            ],
            [
                'nome' => 'Formação ADM',
                'link' => 'https://example.com/formacao-adm',
                'ordem' => 4,
            ],
        ];

        foreach ($patrocinadores as $patrocinador) {
            Sponsor::updateOrCreate(
                ['nome' => $patrocinador['nome']],
                [
                    'link' => $patrocinador['link'],
                    'ordem' => $patrocinador['ordem'],
                    'ativo' => true,
                ],
            );
        }
    }
}
