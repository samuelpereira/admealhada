<?php

use App\Livewire\Site\Schedule;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('the calendar team filter lists only teams from the selected season', function () {
    Carbon::setTestNow('2026-09-25');

    $sport = Sport::factory()->create();
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');

    Team::factory()->for($sport)->for($current)->create([
        'nome' => 'Séniores época actual',
    ]);

    Team::factory()->for($sport)->for($archive)->create([
        'nome' => 'Séniores época arquivo',
    ]);

    Livewire::test(Schedule::class)
        ->assertSet('epoca', '2026/2027')
        ->assertSee('Séniores época actual')
        ->assertDontSee('Séniores época arquivo')
        ->set('epoca', '2024/2025')
        ->assertSee('Séniores época arquivo')
        ->assertDontSee('Séniores época actual');
});

test('changing the season clears a team that does not belong to it', function () {
    Carbon::setTestNow('2026-09-25');

    $sport = Sport::factory()->create();
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');

    $currentTeam = Team::factory()->for($sport)->for($current)->create();
    Team::factory()->for($sport)->for($archive)->create();

    Livewire::test(Schedule::class)
        ->set('teamId', (string) $currentTeam->id)
        ->set('epoca', '2024/2025')
        ->assertSet('teamId', '');
});
