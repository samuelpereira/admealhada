<?php

use App\Livewire\Site\Home;
use App\Models\Game;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('the home page shows only latest results from the current season', function () {
    Carbon::setTestNow('2026-09-25');

    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();

    Game::factory()->for($team)->create([
        'adversario' => 'Ovarense actual',
        'data_hora' => '2026-09-10 18:00:00',
        'estado' => 'terminado',
        'pontos_casa' => 70,
        'pontos_fora' => 60,
    ]);

    Game::factory()->for($team)->create([
        'adversario' => 'Illiabum antiga',
        'data_hora' => '2026-05-10 18:00:00',
        'estado' => 'terminado',
        'pontos_casa' => 50,
        'pontos_fora' => 40,
    ]);

    Livewire::test(Home::class)
        ->assertSee('Ovarense actual')
        ->assertDontSee('Illiabum antiga');
});

test('the home page says there are no results yet when the current season has none', function () {
    Carbon::setTestNow('2026-09-25');

    Livewire::test(Home::class)
        ->assertSee('Ainda não há resultados.')
        ->assertSee(route('calendario').'#resultados', false)
        ->assertDontSee('Resultados a sincronizar pela API.');
});
