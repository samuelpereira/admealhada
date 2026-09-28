<?php

use App\Livewire\Admin\Players\PlayerForm;
use App\Livewire\Admin\Players\PlayersIndex;
use App\Models\Player;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
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
