<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $destaques = [
            [
                'titulo' => 'Inscrições abertas para a próxima época',
                'resumo' => 'Estão abertas as inscrições para os escalões de formação, do Sub-10 ao Sub-16, masculinos e femininos. Vem fazer parte da família mealhadense!',
                'categoria' => 'Clube',
                'destaque' => true,
                'publicado_em' => now()->subDays(8),
            ],
            [
                'titulo' => 'Comunicado aos sócios: Assembleia Geral',
                'resumo' => 'Convocatória para a próxima Assembleia Geral de sócios da Academia Desportiva da Mealhada. Consulta a ordem de trabalhos.',
                'categoria' => 'Sócios',
                'destaque' => false,
                'publicado_em' => now()->subDays(12),
            ],
        ];

        foreach ($destaques as $data) {
            $corpo = collect(fake()->paragraphs(5))
                ->map(fn ($p) => '<p>'.$p.'</p>')
                ->implode("\n");

            News::updateOrCreate(
                ['slug' => Str::slug($data['titulo'])],
                array_merge($data, ['corpo' => $corpo, 'capa' => null]),
            );
        }

        News::factory()->count(9)->create();
    }
}
