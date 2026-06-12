<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Photo;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $seniores = Team::where('slug', 'seniores-masculinos')->first();
        $sub16m = Team::where('slug', 'sub-16-masculinos')->first();

        $albuns = [
            [
                'titulo' => 'Jogo Seniores vs Sangalhos DC',
                'descricao' => 'Imagens do jogo da equipa sénior frente ao Sangalhos DC no Pavilhão Municipal da Mealhada.',
                'data' => now()->subDays(10),
                'team_id' => $seniores?->id,
                'fotos' => 10,
            ],
            [
                'titulo' => 'Festa do Minibasquete',
                'descricao' => 'Um dia cheio de alegria com os mais novos do clube nos escalões Sub-10 e Sub-12.',
                'data' => now()->subDays(25),
                'team_id' => null,
                'fotos' => 12,
            ],
            [
                'titulo' => 'Torneio de Formação Sub-16',
                'descricao' => 'Os Sub-16 masculinos em ação no torneio de pré-época.',
                'data' => now()->subMonths(2),
                'team_id' => $sub16m?->id,
                'fotos' => 8,
            ],
            [
                'titulo' => 'Apresentação das equipas 2025/2026',
                'descricao' => 'Apresentação oficial de todos os escalões da AD Mealhada para a nova temporada.',
                'data' => now()->subMonths(3),
                'team_id' => null,
                'fotos' => 14,
            ],
        ];

        foreach ($albuns as $data) {
            $fotos = $data['fotos'];
            unset($data['fotos']);

            $album = Album::updateOrCreate(
                ['slug' => Str::slug($data['titulo'])],
                array_merge($data, ['capa' => null]),
            );

            $album->photos()->delete();

            for ($i = 1; $i <= $fotos; $i++) {
                Photo::create([
                    'album_id' => $album->id,
                    'caminho' => null,
                    'legenda' => 'Foto '.$i,
                    'ordem' => $i,
                ]);
            }
        }
    }
}
