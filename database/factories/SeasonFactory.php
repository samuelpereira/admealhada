<?php

namespace Database\Factories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Season>
 */
class SeasonFactory extends Factory
{
    protected $model = Season::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = fake()->unique()->numberBetween(2010, 2040);
        $slug = $startYear.'-'.($startYear + 1);

        while (Season::query()->where('slug', $slug)->exists()) {
            $startYear = fake()->unique()->numberBetween(2010, 2040);
            $slug = $startYear.'-'.($startYear + 1);
        }

        return [
            'name' => $startYear.'/'.($startYear + 1),
            'slug' => $slug,
            'starts_at' => Carbon::create($startYear, 9, 1),
            'ends_at' => Carbon::create($startYear + 1, 8, 31),
            'is_current' => false,
        ];
    }

    public function current(): static
    {
        return $this->state(fn (): array => [
            'is_current' => true,
        ]);
    }
}
