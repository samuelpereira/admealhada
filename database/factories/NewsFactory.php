<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        $titulo = rtrim(fake()->sentence(6), '.');

        return [
            'titulo' => $titulo,
            'slug' => Str::slug($titulo).'-'.fake()->unique()->numberBetween(1, 100000),
            'resumo' => fake()->paragraph(2),
            'corpo' => collect(fake()->paragraphs(5))->map(fn ($p) => '<p>'.$p.'</p>')->implode("\n"),
            'capa' => null,
            'categoria' => fake()->randomElement(['Clube', 'Sócios', 'Jogos', 'Formação']),
            'destaque' => false,
            'publicado_em' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
