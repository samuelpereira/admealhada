<?php

namespace Database\Factories;

use App\Models\Album;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Album>
 */
class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition(): array
    {
        $titulo = fake()->sentence(3);

        return [
            'team_id' => null,
            'titulo' => $titulo,
            'slug' => Str::slug($titulo),
            'descricao' => fake()->optional()->paragraph(),
            'capa' => null,
            'data' => fake()->optional()->date(),
        ];
    }
}
