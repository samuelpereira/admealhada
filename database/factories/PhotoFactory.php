<?php

namespace Database\Factories;

use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        return [
            'album_id' => null,
            'caminho' => 'galerias/'.fake()->uuid().'.jpg',
            'legenda' => fake()->optional()->sentence(),
            'ordem' => 0,
        ];
    }
}
