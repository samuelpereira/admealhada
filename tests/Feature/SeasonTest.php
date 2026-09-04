<?php

use App\Models\Game;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Support\Carbon;

test('derives the september-to-august season from a date', function (string $date, string $name, string $slug) {
    $attributes = Season::attributesForDate(Carbon::parse($date));

    expect($attributes['name'])->toBe($name)
        ->and($attributes['slug'])->toBe($slug)
        ->and($attributes['starts_at'])->toBe('2025-09-01')
        ->and($attributes['ends_at'])->toBe('2026-08-31');
})->with([
    ['2025-09-01', '2025/2026', '2025-2026'],
    ['2026-01-15', '2025/2026', '2025-2026'],
    ['2026-08-31', '2025/2026', '2025-2026'],
]);

test('migration seeds exactly one current season', function () {
    expect(Season::query()->current()->count())->toBe(1);
});

test('backfill creates a season for each game year and marks the current one', function () {
    Season::query()->delete();

    $this->travelTo('2026-03-15 12:00:00');

    $team = Team::factory()->for(Sport::factory())->create();

    Game::factory()->for($team)->create(['data_hora' => '2024-10-05 18:00:00']);
    Game::factory()->for($team)->create(['data_hora' => '2025-10-05 18:00:00']);

    Season::backfillFromGames();

    expect(Season::query()->where('slug', '2024-2025')->exists())->toBeTrue()
        ->and(Season::current()?->name)->toBe('2025/2026')
        ->and(Season::query()->current()->count())->toBe(1);
});

test('next season is the following september year', function () {
    $season = Season::ensureForName('2025/2026');

    $next = $season->next();

    expect($next->name)->toBe('2026/2027')
        ->and($next->slug)->toBe('2026-2027')
        ->and($next->starts_at?->toDateString())->toBe('2026-09-01');
});

test('current season public index url is the short listing', function () {
    $season = Season::ensureCurrent();

    expect($season->publicIndexUrl())->toBe(route('equipas'));
});

test('archive season public index url includes the season slug', function () {
    $season = Season::ensureForName('2024/2025');

    expect($season->publicIndexUrl())->toBe(route('equipas.season', $season));
});

test('marking a season current clears the previous current flag', function () {
    $previous = Season::query()->current()->first();
    $next = Season::factory()->create();

    $next->markAsCurrent();

    expect($next->fresh()->is_current)->toBeTrue()
        ->and($previous?->fresh()->is_current)->toBeFalse()
        ->and(Season::query()->current()->count())->toBe(1);
});
