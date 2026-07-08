<?php

namespace Database\Seeders;

use App\Models\Sponsor;
use App\Models\Team;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $patrocinadoresGerais = [
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

        foreach ($patrocinadoresGerais as $patrocinador) {
            Sponsor::updateOrCreate(
                ['nome' => $patrocinador['nome']],
                [
                    'link' => $patrocinador['link'],
                    'tipo' => Sponsor::TIPO_GERAL,
                    'ordem' => $patrocinador['ordem'],
                    'ativo' => true,
                ],
            );
        }

        $seniores = Team::where('slug', 'seniores-masculinos')->first();
        $sub16m = Team::where('slug', 'sub-16-masculinos')->first();

        if ($seniores) {
            $principalSeniores = Sponsor::updateOrCreate(
                ['nome' => 'Patrocinador Seniores A'],
                [
                    'link' => 'https://example.com/patrocinador-seniores-a',
                    'tipo' => Sponsor::TIPO_PRINCIPAL,
                    'ordem' => 1,
                    'ativo' => true,
                ],
            );
            $principalSeniores->teams()->syncWithoutDetaching([$seniores->id]);

            $principalSenioresB = Sponsor::updateOrCreate(
                ['nome' => 'Patrocinador Seniores B'],
                [
                    'link' => 'https://example.com/patrocinador-seniores-b',
                    'tipo' => Sponsor::TIPO_PRINCIPAL,
                    'ordem' => 2,
                    'ativo' => true,
                ],
            );
            $principalSenioresB->teams()->syncWithoutDetaching([$seniores->id]);
        }

        if ($sub16m) {
            $principalSub16 = Sponsor::updateOrCreate(
                ['nome' => 'Patrocinador Sub-16'],
                [
                    'link' => 'https://example.com/patrocinador-sub16',
                    'tipo' => Sponsor::TIPO_PRINCIPAL,
                    'ordem' => 1,
                    'ativo' => true,
                ],
            );
            $principalSub16->teams()->syncWithoutDetaching([$sub16m->id]);
        }
    }
}
