<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        $emCasa = fake()->boolean();

        return [
            'team_id' => null,
            'adversario' => fake()->randomElement([
                'Sangalhos DC', 'CD Anadia', 'GD Aguada', 'CB Oliveira do Bairro',
                'Académica de Coimbra', 'Ginásio Figueirense', 'Galitos Barreiro',
                'Ovarense', 'Illiabum Clube', 'CA Madeira', 'Lusitânia', 'CD Póvoa',
            ]),
            'data_hora' => fake()->dateTimeBetween('-2 months', '+2 months'),
            'local' => $emCasa ? 'Pavilhão Municipal da Mealhada' : 'Pavilhão do adversário',
            'casa_fora' => $emCasa ? 'casa' : 'fora',
            'competicao' => 'Campeonato Regional',
            'jornada' => fake()->numberBetween(1, 22),
            'pontos_casa' => null,
            'pontos_fora' => null,
            'estado' => 'agendado',
        ];
    }

    public function terminado(): static
    {
        return $this->state(function () {
            return [
                'data_hora' => fake()->dateTimeBetween('-2 months', '-2 days'),
                'estado' => 'terminado',
                'pontos_casa' => fake()->numberBetween(45, 95),
                'pontos_fora' => fake()->numberBetween(45, 95),
            ];
        });
    }

    public function agendado(): static
    {
        return $this->state(fn () => [
            'data_hora' => fake()->dateTimeBetween('+2 days', '+2 months'),
            'estado' => 'agendado',
            'pontos_casa' => null,
            'pontos_fora' => null,
        ]);
    }
}
