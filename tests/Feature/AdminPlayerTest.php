<?php

use App\Livewire\Admin\Players\PlayerForm;
use App\Livewire\Admin\Players\PlayersIndex;
use App\Models\Player;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Services\Fpb\FpbGamesImporter;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('an admin saves a player birth date after the name', function () {
    $this->actingAs(User::factory()->admin()->create());

    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();

    Livewire::test(PlayerForm::class)
        ->set('team_id', $team->id)
        ->set('nome', 'Miguel Costa')
        ->set('dataNascimento', '2011-03-17')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.players.index'));

    $player = Player::query()->sole();

    expect($player->nome)->toBe('Miguel Costa')
        ->and($player->ativo)->toBeTrue()
        ->and($player->data_nascimento?->toDateString())->toBe('2011-03-17');

    Livewire::test(PlayerForm::class, ['player' => $player])
        ->assertSet('dataNascimento', '2011-03-17');

    Livewire::test(PlayersIndex::class)
        ->assertSeeInOrder(['Miguel Costa', '17/03/2011']);
});

test('the public roster keeps the birth year and hides the full birth date', function () {
    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();
    Player::factory()->for($team)->create([
        'nome' => 'Miguel Costa',
        'ano_nascimento' => 2008,
        'data_nascimento' => '2011-03-17',
        'papel' => 'jogador',
    ]);

    $this->get($team->publicShowUrl())
        ->assertOk()
        ->assertSee('Miguel Costa')
        ->assertSee('2008')
        ->assertDontSee('17/03/2011')
        ->assertDontSee('2011-03-17');
});

test('the player list follows the selected season and its teams', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();
    $currentTeam = Team::factory()->for($sport)->for($current)->create(['nome' => 'Equipa actual']);
    $archiveTeam = Team::factory()->for($sport)->for($archive)->create(['nome' => 'Equipa arquivo']);

    Player::factory()->for($currentTeam)->create(['nome' => 'Jogador actual']);
    Player::factory()->for($archiveTeam)->create(['nome' => 'Jogador arquivo']);

    Livewire::test(PlayersIndex::class)
        ->assertSee('Jogador actual')
        ->assertSee('Equipa actual')
        ->assertDontSee('Jogador arquivo')
        ->assertDontSee('Equipa arquivo')
        ->set('epoca', $archive->slug)
        ->set('team', (string) $archiveTeam->id)
        ->assertSee('Jogador arquivo')
        ->assertDontSee('Jogador actual')
        ->set('epoca', $current->slug)
        ->assertSet('team', '')
        ->assertSee('Jogador actual')
        ->assertDontSee('Jogador arquivo');
});

test('the player list filters by name', function () {
    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();
    Player::factory()->for($team)->create(['nome' => 'Gonçalo Dias']);
    Player::factory()->for($team)->create(['nome' => 'Miguel Costa']);

    Livewire::test(PlayersIndex::class)
        ->set('pesquisa', 'goncalo')
        ->assertSee('Gonçalo Dias')
        ->assertDontSee('Miguel Costa');
});

test('a hidden player stays off the public team page', function () {
    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();
    Player::factory()->for($team)->create([
        'nome' => 'Jogador visivel',
        'papel' => 'jogador',
        'ativo' => true,
    ]);
    Player::factory()->for($team)->create([
        'nome' => 'Jogador oculto',
        'papel' => 'jogador',
        'ativo' => false,
    ]);

    $this->get($team->publicShowUrl())
        ->assertOk()
        ->assertSee('Jogador visivel')
        ->assertDontSee('Jogador oculto');

    Livewire::test(PlayersIndex::class)
        ->assertSee('Jogador oculto')
        ->assertSee('Oculto');
});

test('a player wearing number zero is shown on the team card', function () {
    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();
    Player::factory()->for($team)->create([
        'nome' => 'Jogador zero',
        'papel' => 'jogador',
        'numero' => 0,
    ]);

    $this->get($team->publicShowUrl())
        ->assertOk()
        ->assertSee('Jogador zero')
        ->assertSee('text-white">0</span>', false);
});

test('an fpb import shows a new player and keeps a hidden player hidden', function () {
    Http::fake([
        'https://fpb.test/equipa' => Http::response(<<<'HTML'
<div class="roster__player">
<a href="/atletas/4242"></a>
<h2 class="header__heading">Miguel Costa</h2>
<div class="header_position">Base</div>
<div class="header_jnumber">7</div>
</div>
HTML),
    ]);

    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create([
        'fpb_url' => 'https://fpb.test/equipa',
    ]);

    app(FpbGamesImporter::class)->importPlayers($team, prune: false);

    $player = Player::query()->where('fpb_id', '4242')->first();

    expect($player)->not->toBeNull()
        ->and($player->nome)->toBe('Miguel Costa')
        ->and($player->ativo)->toBeTrue();

    $player->update(['ativo' => false]);

    app(FpbGamesImporter::class)->importPlayers($team, prune: false);

    expect($player->fresh()->ativo)->toBeFalse();
});

test('saving a birth date updates every roster row of that athlete', function () {
    $this->actingAs(User::factory()->admin()->create());

    $sport = Sport::factory()->create();
    $season = Season::ensureCurrent();
    $sub16 = Team::factory()->for($sport)->for($season)->create(['nome' => 'Sub-16']);
    $sub18 = Team::factory()->for($sport)->for($season)->create(['nome' => 'Sub-18']);

    $edited = Player::factory()->for($sub16)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'numero' => 7,
    ]);
    $sameAthlete = Player::factory()->for($sub18)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'numero' => 12,
    ]);
    $otherAthlete = Player::factory()->for($sub16)->create([
        'fpb_id' => '9999',
        'nome' => 'Ana Lopes',
        'data_nascimento' => '2010-01-02',
    ]);
    $manual = Player::factory()->for($sub18)->create([
        'nome' => 'Sem Ficha',
        'data_nascimento' => '2009-05-05',
    ]);

    Livewire::test(PlayerForm::class, ['player' => $edited])
        ->set('dataNascimento', '2011-03-17')
        ->call('save')
        ->assertHasNoErrors();

    expect($sameAthlete->fresh()->data_nascimento?->toDateString())->toBe('2011-03-17')
        ->and($sameAthlete->fresh()->numero)->toBe(12)
        ->and($otherAthlete->fresh()->data_nascimento?->toDateString())->toBe('2010-01-02')
        ->and($manual->fresh()->data_nascimento?->toDateString())->toBe('2009-05-05');
});

test('clearing a birth date clears it on every roster row of that athlete', function () {
    $this->actingAs(User::factory()->admin()->create());

    $sport = Sport::factory()->create();
    $season = Season::ensureCurrent();
    $sub16 = Team::factory()->for($sport)->for($season)->create();
    $sub18 = Team::factory()->for($sport)->for($season)->create();

    $edited = Player::factory()->for($sub16)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-03-17',
    ]);
    $sameAthlete = Player::factory()->for($sub18)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-03-17',
    ]);

    Livewire::test(PlayerForm::class, ['player' => $edited])
        ->set('dataNascimento', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($edited->fresh()->data_nascimento)->toBeNull()
        ->and($sameAthlete->fresh()->data_nascimento)->toBeNull();
});

test('an fpb import copies the birth date onto a new roster row', function () {
    Http::fake([
        'https://fpb.test/equipa' => Http::response(<<<'HTML'
<div class="roster__player">
<a href="/atletas/4242"></a>
<h2 class="header__heading">Miguel Costa</h2>
<div class="header_position">Base</div>
<div class="header_jnumber">9</div>
</div>
HTML),
    ]);

    $sport = Sport::factory()->create();
    $current = Season::ensureCurrent();
    $previous = Season::ensureForName('2025/2026');
    $previousTeam = Team::factory()->for($sport)->for($previous)->create();
    $nextTeam = Team::factory()->for($sport)->for($current)->create([
        'fpb_url' => 'https://fpb.test/equipa',
    ]);

    Player::factory()->for($previousTeam)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-03-17',
    ]);

    app(FpbGamesImporter::class)->importPlayers($nextTeam, prune: false);

    $imported = Player::query()->where('team_id', $nextTeam->id)->where('fpb_id', '4242')->first();

    expect($imported)->not->toBeNull()
        ->and($imported->data_nascimento?->toDateString())->toBe('2011-03-17')
        ->and($imported->numero)->toBe(9);
});

test('an fpb import fills a missing birth date and keeps one already stored', function () {
    Http::fake([
        'https://fpb.test/equipa' => Http::response(<<<'HTML'
<div class="roster__player">
<a href="/atletas/4242"></a>
<h2 class="header__heading">Miguel Costa</h2>
<div class="header_jnumber">7</div>
</div>
<div class="roster__player">
<a href="/atletas/5151"></a>
<h2 class="header__heading">Ana Lopes</h2>
<div class="header_jnumber">4</div>
</div>
HTML),
    ]);

    $sport = Sport::factory()->create();
    $season = Season::ensureCurrent();
    $sourceTeam = Team::factory()->for($sport)->for($season)->create();
    $team = Team::factory()->for($sport)->for($season)->create([
        'fpb_url' => 'https://fpb.test/equipa',
    ]);

    Player::factory()->for($sourceTeam)->create([
        'fpb_id' => '4242',
        'data_nascimento' => '2011-03-17',
    ]);
    Player::factory()->for($sourceTeam)->create([
        'fpb_id' => '5151',
        'data_nascimento' => '2012-08-01',
    ]);
    $missing = Player::factory()->for($team)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'data_nascimento' => null,
    ]);
    $stored = Player::factory()->for($team)->create([
        'fpb_id' => '5151',
        'nome' => 'Ana Lopes',
        'data_nascimento' => '2010-01-01',
    ]);

    app(FpbGamesImporter::class)->importPlayers($team, prune: false);

    expect($missing->fresh()->data_nascimento?->toDateString())->toBe('2011-03-17')
        ->and($stored->fresh()->data_nascimento?->toDateString())->toBe('2010-01-01');
});
