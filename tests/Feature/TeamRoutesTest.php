<?php

use App\Livewire\Site\About;
use App\Livewire\Site\Home;
use App\Models\Player;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

function seasonTeams(): array
{
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    $currentTeam = Team::factory()->for($sport)->for($current)->create([
        'nome' => 'Séniores época actual',
        'slug' => 'seniores-masculinos',
    ]);

    $archiveTeam = Team::factory()->for($sport)->for($archive)->create([
        'nome' => 'Séniores época arquivo',
        'slug' => 'seniores-masculinos',
    ]);

    return compact('current', 'archive', 'sport', 'currentTeam', 'archiveTeam');
}

test('current season team uses the short url', function () {
    ['currentTeam' => $currentTeam] = seasonTeams();

    $this->get(route('equipas.show', $currentTeam))
        ->assertOk()
        ->assertSee('Séniores época actual');

    expect($currentTeam->publicShowUrl())->toEndWith('/equipas/seniores-masculinos');
});

test('archive team uses the season in the url', function () {
    ['archive' => $archive, 'archiveTeam' => $archiveTeam] = seasonTeams();

    $this->get(route('equipas.show.season', ['season' => $archive, 'team' => $archiveTeam]))
        ->assertOk()
        ->assertSee('Séniores época arquivo')
        ->assertSee('2024/2025');

    expect($archiveTeam->publicShowUrl())->toBe(route('equipas.show.season', [
        'season' => $archive,
        'team' => $archiveTeam,
    ]));
});

test('current season long urls permanently redirect to the short urls', function () {
    ['current' => $current, 'currentTeam' => $currentTeam] = seasonTeams();

    $this->get(route('equipas.season', $current))
        ->assertStatus(301)
        ->assertRedirect(route('equipas'));

    $this->get(route('equipas.show.season', ['season' => $current, 'team' => $currentTeam]))
        ->assertStatus(301)
        ->assertRedirect(route('equipas.show', $currentTeam));
});

test('unknown team slug returns 404', function () {
    seasonTeams();

    $this->get('/equipas/nao-existe')->assertNotFound();
    $this->get('/equipas/2024-2025/nao-existe')->assertNotFound();
});

test('archive-only slugs are not available on the current season short url', function () {
    seasonTeams();

    $archiveOnly = Team::factory()->for(Sport::factory())->for(Season::ensureForName('2023/2024'))->create([
        'slug' => 'sub-14-arquivo',
        'nome' => 'Sub-14 arquivo',
    ]);

    $this->get('/equipas/sub-14-arquivo')->assertNotFound();
    $this->get($archiveOnly->publicShowUrl())->assertOk()->assertSee('Sub-14 arquivo');
});

test('team listings stay on their season', function () {
    ['archive' => $archive] = seasonTeams();

    $this->get(route('equipas'))
        ->assertOk()
        ->assertSee('Séniores época actual')
        ->assertDontSee('Séniores época arquivo');

    $this->get(route('equipas.season', $archive))
        ->assertOk()
        ->assertSee('Séniores época arquivo')
        ->assertDontSee('Séniores época actual');
});

test('home and clube counts ignore teams and players from other seasons', function () {
    ['currentTeam' => $currentTeam, 'archiveTeam' => $archiveTeam] = seasonTeams();

    Player::factory()->for($currentTeam)->create();
    Player::factory()->count(3)->for($archiveTeam)->create();

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Séniores época actual')
        ->assertDontSee('Séniores época arquivo');

    Livewire::test(Home::class)
        ->assertViewHas('totalEquipas', 1)
        ->assertViewHas('totalJogadores', 1);

    Livewire::test(About::class)
        ->assertViewHas('totalEquipas', 1)
        ->assertViewHas('totalJogadores', 1);
});

test('home and clube count one player when the same fpb id is on two current teams', function () {
    ['current' => $current, 'sport' => $sport, 'currentTeam' => $currentTeam] = seasonTeams();
    $secondTeam = Team::factory()->for($sport)->for($current)->create([
        'nome' => 'Sub-16 época actual',
        'slug' => 'sub-16-masculinos',
    ]);

    Player::factory()->for($currentTeam)->create(['fpb_id' => 'fpb-100']);
    Player::factory()->for($secondTeam)->create(['fpb_id' => 'fpb-100']);

    Livewire::test(Home::class)->assertViewHas('totalJogadores', 1);
    Livewire::test(About::class)->assertViewHas('totalJogadores', 1);
});

test('sitemap lists short urls for current teams and long urls for archive teams', function () {
    ['current' => $current, 'archive' => $archive, 'currentTeam' => $currentTeam, 'archiveTeam' => $archiveTeam] = seasonTeams();

    Cache::flush();

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee($currentTeam->publicShowUrl(), false)
        ->assertSee($archiveTeam->publicShowUrl(), false)
        ->assertSee($archive->publicIndexUrl(), false)
        ->assertDontSee(route('equipas.show.season', ['season' => $current, 'team' => $currentTeam]), false);
});

test('admin edits teams by id when slugs are duplicated across seasons', function () {
    ['archiveTeam' => $archiveTeam] = seasonTeams();

    expect(route('admin.teams.edit', $archiveTeam))->toContain('/equipas/'.$archiveTeam->id.'/editar');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.teams.edit', $archiveTeam))
        ->assertOk();
});
