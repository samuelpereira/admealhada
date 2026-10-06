<?php

use App\Livewire\Admin\Games\GamesIndex;
use App\Models\Game;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use Livewire\Livewire;

test('the game list follows the selected season and its teams', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();
    $currentTeam = Team::factory()->for($sport)->for($current)->create(['nome' => 'Equipa actual']);
    $archiveTeam = Team::factory()->for($sport)->for($archive)->create(['nome' => 'Equipa arquivo']);

    Game::factory()->for($currentTeam)->create(['adversario' => 'Adversario actual']);
    Game::factory()->for($archiveTeam)->create(['adversario' => 'Adversario arquivo']);

    Livewire::test(GamesIndex::class)
        ->assertSee('Adversario actual')
        ->assertSee('Equipa actual')
        ->assertDontSee('Adversario arquivo')
        ->assertDontSee('Equipa arquivo')
        ->set('epoca', $archive->slug)
        ->set('team', (string) $archiveTeam->id)
        ->assertSee('Adversario arquivo')
        ->assertDontSee('Adversario actual')
        ->set('epoca', $current->slug)
        ->assertSet('team', '')
        ->assertSee('Adversario actual')
        ->assertDontSee('Adversario arquivo');
});

test('the game list filters by team or opponent name', function () {
    $season = Season::ensureCurrent();
    $sport = Sport::factory()->create();
    $seniores = Team::factory()->for($sport)->for($season)->create(['nome' => 'Seniores']);
    $sub16 = Team::factory()->for($sport)->for($season)->create(['nome' => 'Sub-16']);

    Game::factory()->for($seniores)->create(['adversario' => 'Ovarense']);
    Game::factory()->for($sub16)->create(['adversario' => 'Illiabum']);
    Game::factory()->for($seniores)->create(['adversario' => 'São João']);

    Livewire::test(GamesIndex::class)
        ->set('pesquisa', 'ovar')
        ->assertSee('Ovarense')
        ->assertDontSee('Illiabum')
        ->set('pesquisa', 'sub-16')
        ->assertSee('Illiabum')
        ->assertDontSee('Ovarense')
        ->set('pesquisa', 'sao')
        ->assertSee('São João')
        ->assertDontSee('Illiabum');
});
