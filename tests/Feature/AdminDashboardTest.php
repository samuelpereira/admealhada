<?php

use App\Livewire\Admin\Dashboard;
use App\Models\Game;
use App\Models\Player;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('the admin dashboard counts teams players and games from the current season', function () {
    Carbon::setTestNow('2026-09-25');
    $this->actingAs(User::factory()->admin()->create());

    $sport = Sport::factory()->create();
    $currentTeam = Team::factory()->for($sport)->for(Season::ensureCurrent())->create();
    $archiveTeam = Team::factory()->for($sport)->for(Season::ensureForName('2024/2025'))->create();

    Player::factory()->for($currentTeam)->create();
    Player::factory()->count(2)->for($archiveTeam)->create();
    Game::factory()->for($currentTeam)->create();
    Game::factory()->count(3)->for($archiveTeam)->create();

    Livewire::test(Dashboard::class)
        ->assertViewHas('cards', function (array $cards): bool {
            $values = collect($cards)->pluck('value', 'label');

            return $values['Equipas'] === 1
                && $values['Jogadores'] === 1
                && $values['Jogos'] === 1;
        });
});

test('the admin dashboard counts one player when the same fpb id is on two current teams', function () {
    Carbon::setTestNow('2026-09-25');
    $this->actingAs(User::factory()->admin()->create());

    $sport = Sport::factory()->create();
    $season = Season::ensureCurrent();
    $seniores = Team::factory()->for($sport)->for($season)->create();
    $sub16 = Team::factory()->for($sport)->for($season)->create();

    Player::factory()->for($seniores)->create(['fpb_id' => 'fpb-100']);
    Player::factory()->for($sub16)->create(['fpb_id' => 'fpb-100']);
    Player::factory()->for($seniores)->create(['fpb_id' => null]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('cards', function (array $cards): bool {
            $values = collect($cards)->pluck('value', 'label');

            return $values['Jogadores'] === 2;
        });
});

test('the admin dashboard lists current-season players with a birthday this month', function () {
    Carbon::setTestNow('2026-10-06');
    $this->actingAs(User::factory()->admin()->create());

    $sport = Sport::factory()->create();
    $currentTeam = Team::factory()->for($sport)->for(Season::ensureCurrent())->create(['nome' => 'Seniores']);
    $archiveTeam = Team::factory()->for($sport)->for(Season::ensureForName('2024/2025'))->create();

    Player::factory()->for($currentTeam)->create([
        'nome' => 'Ana Outubro',
        'data_nascimento' => '2012-10-20',
    ]);
    Player::factory()->for($currentTeam)->create([
        'nome' => 'Bruno Outubro',
        'data_nascimento' => '2011-10-03',
    ]);
    Player::factory()->for($currentTeam)->create([
        'nome' => 'Carla Marco',
        'data_nascimento' => '2010-03-17',
    ]);
    Player::factory()->for($currentTeam)->create([
        'nome' => 'Sem Data',
    ]);
    Player::factory()->for($archiveTeam)->create([
        'nome' => 'Arquivo Outubro',
        'data_nascimento' => '2012-10-09',
    ]);

    Livewire::test(Dashboard::class)
        ->assertSee('Aniversários em outubro')
        ->assertSeeInOrder(['Bruno Outubro', '03/10', 'Ana Outubro', '20/10'])
        ->assertDontSee('Carla Marco')
        ->assertDontSee('Sem Data')
        ->assertDontSee('Arquivo Outubro');
});

test('the admin dashboard lists one birthday when the same athlete is on two current teams', function () {
    Carbon::setTestNow('2026-10-06');
    $this->actingAs(User::factory()->admin()->create());

    $sport = Sport::factory()->create();
    $season = Season::ensureCurrent();
    $sub16 = Team::factory()->for($sport)->for($season)->create(['nome' => 'Sub-16']);
    $sub18 = Team::factory()->for($sport)->for($season)->create(['nome' => 'Sub-18']);
    $archive = Team::factory()->for($sport)->for(Season::ensureForName('2024/2025'))->create(['nome' => 'Sub-14']);

    Player::factory()->for($sub16)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-10-03',
    ]);
    Player::factory()->for($sub18)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-10-03',
    ]);
    Player::factory()->for($archive)->create([
        'fpb_id' => '4242',
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-10-03',
    ]);
    Player::factory()->for($sub16)->create([
        'fpb_id' => '9999',
        'nome' => 'Ana Outubro',
        'data_nascimento' => '2012-10-20',
    ]);

    Livewire::test(Dashboard::class)
        ->assertViewHas('aniversarios', fn ($aniversarios): bool => $aniversarios->count() === 2)
        ->assertSeeInOrder(['Miguel Costa', 'Sub-16, Sub-18', '03/10', 'Ana Outubro', '20/10'])
        ->assertDontSee('Sub-14');
});
