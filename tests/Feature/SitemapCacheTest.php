<?php

use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Support\SitemapCache;
use Illuminate\Support\Facades\Cache;

test('saving a team forgets the sitemap cache', function () {
    Cache::put(SitemapCache::KEY, '<xml>stale</xml>');

    Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();

    expect(Cache::has(SitemapCache::KEY))->toBeFalse();
});

test('sitemap omits archive seasons that have no teams', function () {
    $current = Season::ensureCurrent();
    $empty = Season::ensureForName('2023/2024');
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    Team::factory()->for($sport)->for($current)->create(['slug' => 'seniores-actual']);
    Team::factory()->for($sport)->for($archive)->create(['slug' => 'seniores-arquivo']);

    Cache::flush();

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee($archive->publicIndexUrl(), false)
        ->assertDontSee($empty->publicIndexUrl(), false);
});
