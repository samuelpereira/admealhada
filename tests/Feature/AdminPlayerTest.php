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
