<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Player>
 */
class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'team_id' => null,
            'nome' => fake()->name('male'),
            'numero' => fake()->numberBetween(4, 15),
            'posicao' => fake()->randomElement(['Base', 'Extremo', 'Ala', 'Ala-Poste', 'Poste']),
            'ano_nascimento' => fake()->numberBetween(2005, 2010),
            'foto' => null,
            'papel' => 'jogador',
            'ordem' => 0,
        ];
    }

    public function treinador(): static
    {
        return $this->state(fn () => [
            'numero' => null,
            'posicao' => null,
            'papel' => 'Treinador',
        ]);
    }
}
