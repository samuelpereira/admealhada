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
