<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $sport = Sport::where('slug', 'basquetebol')->firstOrFail();

        $teams = [
            [
                'nome' => 'Seniores Masculinos',
                'escalao' => 'Seniores',
                'genero' => 'M',
                'competicao' => 'CN2 - Campeonato Nacional da 2.ª Divisão',
                'fpb_url' => 'https://www.fpb.pt/equipa/equipa_57944',
                'descricao' => 'A equipa principal do clube compete no Campeonato Nacional da 2.ª Divisão (CN2), representando a Mealhada no basquetebol nacional.',
            ],
            [
                'nome' => 'Sub-16 Masculinos',
                'escalao' => 'Sub-16',
                'genero' => 'M',
                'competicao' => 'Campeonato Distrital Sub-16 Masculinos',
                'fpb_url' => 'https://www.fpb.pt/equipa/equipa_59129',
                'descricao' => 'Escalão de formação que prepara os atletas para a transição ao basquetebol sénior.',
            ],
            [
                'nome' => 'Sub-16 Femininos',
                'escalao' => 'Sub-16',
                'genero' => 'F',
                'competicao' => 'Campeonato Distrital Sub-16 Femininos',
                'fpb_url' => 'https://www.fpb.pt/equipa/equipa_59104',
                'descricao' => 'Equipa feminina de formação do escalão Sub-16.',
            ],
            [
                'nome' => 'Sub-14 Masculinos',
                'escalao' => 'Sub-14',
                'genero' => 'M',
                'competicao' => 'Campeonato Distrital Sub-14 Masculinos',
                'fpb_url' => 'https://www.fpb.pt/equipa/equipa_59168',
                'descricao' => 'Escalão de formação focado no desenvolvimento técnico e tático dos jovens atletas.',
            ],
            [
                'nome' => 'Sub-14 Femininos',
                'escalao' => 'Sub-14',
                'genero' => 'F',
                'competicao' => 'Campeonato Distrital Sub-14 Femininos',
                'fpb_url' => 'https://www.fpb.pt/equipa/equipa_59141',
                'descricao' => 'Equipa feminina de formação do escalão Sub-14.',
            ],
            [
                'nome' => 'Sub-12 (Minibasquete)',
                'escalao' => 'Sub-12',
                'genero' => null,
                'competicao' => 'Minibasquete',
                'descricao' => 'Iniciação ao basquetebol no quadro do minibasquete, com encontros e festas de mini.',
            ],
            [
                'nome' => 'Sub-10 (Minibasquete)',
                'escalao' => 'Sub-10',
                'genero' => null,
                'competicao' => 'Minibasquete',
                'descricao' => 'Primeiros passos no basquetebol, com foco na diversão, coordenação e espírito de equipa.',
            ],
        ];

        foreach ($teams as $i => $data) {
            Team::updateOrCreate(
                ['slug' => Str::slug($data['nome'])],
                array_merge($data, [
                    'sport_id' => $sport->id,
                    'foto' => null,
                    'ordem' => $i,
                ]),
            );
        }
    }
}
