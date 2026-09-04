<?php

use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;

test('belongs to a season', function () {
    $season = Season::factory()->create();
    $team = Team::factory()->for(Sport::factory())->for($season)->create();

    expect($team->season->is($season))->toBeTrue();
});

test('allows the same slug in different seasons', function () {
    $first = Season::factory()->create();
    $second = Season::factory()->create();
    $sport = Sport::factory()->create();

    Team::factory()->for($sport)->for($first)->create(['slug' => 'seniores-masculinos']);
    Team::factory()->for($sport)->for($second)->create(['slug' => 'seniores-masculinos']);

    expect(Team::query()->where('slug', 'seniores-masculinos')->count())->toBe(2);
});

test('admin label appends the season name for archive teams', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    $currentTeam = Team::factory()->for($sport)->for($current)->create(['nome' => 'Séniores']);
    $archiveTeam = Team::factory()->for($sport)->for($archive)->create(['nome' => 'Séniores']);

    expect($currentTeam->adminLabel())->toBe('Séniores')
        ->and($archiveTeam->adminLabel())->toBe('Séniores (2024/2025)');
});

test('current season scope only returns teams of the current season', function () {
    $current = Season::ensureCurrent();
    $previous = Season::factory()->create();
    $sport = Sport::factory()->create();

    $currentTeam = Team::factory()->for($sport)->for($current)->create();
    Team::factory()->for($sport)->for($previous)->create();

    expect(Team::query()->currentSeason()->pluck('id')->all())->toBe([$currentTeam->id]);
});
