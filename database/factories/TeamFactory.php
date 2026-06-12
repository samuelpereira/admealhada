<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $nome = rtrim(fake()->sentence(3), '.');

        return [
            'sport_id' => null,
            'nome' => $nome,
            'slug' => Str::slug($nome),
            'escalao' => fake()->randomElement(['Sénior', 'Sub-16', 'Sub-14']),
            'genero' => fake()->randomElement(['M', 'F']),
            'competicao' => null,
            'fpb_url' => null,
            'descricao' => null,
            'foto' => null,
            'ordem' => 0,
        ];
    }
}
