<?php

use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Services\Fpb\FpbGamesImporter;

test('fpb import without an argument only uses current season teams with an fpb url', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    $currentTeam = Team::factory()->for($sport)->for($current)->create([
        'nome' => 'Séniores actual',
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_1/',
    ]);
    Team::factory()->for($sport)->for($archive)->create([
        'nome' => 'Séniores arquivo',
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_2/',
    ]);

    $this->mock(FpbGamesImporter::class, function ($mock) use ($currentTeam) {
        $mock->shouldReceive('import')
            ->once()
            ->withArgs(fn (Team $team): bool => $team->is($currentTeam))
            ->andReturn(['created' => 0, 'updated' => 0, 'deleted' => 0, 'total' => 0]);
        $mock->shouldReceive('importPlayers')
            ->once()
            ->withArgs(fn (Team $team): bool => $team->is($currentTeam))
            ->andReturn(['created' => 0, 'updated' => 0, 'deleted' => 0, 'total' => 0]);
    });

    $this->artisan('fpb:import')
        ->expectsOutputToContain('Séniores actual')
        ->assertSuccessful();
});

test('fpb import without an argument skips current season teams that have no fpb url', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    Team::factory()->for($sport)->for($current)->create(['fpb_url' => null]);
    Team::factory()->for($sport)->for($archive)->create([
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_2/',
    ]);

    $this->mock(FpbGamesImporter::class, function ($mock) {
        $mock->shouldNotReceive('import');
        $mock->shouldNotReceive('importPlayers');
    });

    $this->artisan('fpb:import')
        ->expectsOutput('Nenhuma equipa com link FPB definido.')
        ->assertSuccessful();
});

test('fpb import resolves a slug against the current season', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    $currentTeam = Team::factory()->for($sport)->for($current)->create([
        'slug' => 'seniores-masculinos',
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_1/',
    ]);
    Team::factory()->for($sport)->for($archive)->create([
        'slug' => 'seniores-masculinos',
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_2/',
    ]);

    $this->mock(FpbGamesImporter::class, function ($mock) use ($currentTeam) {
        $mock->shouldReceive('import')
            ->once()
            ->withArgs(fn (Team $team): bool => $team->is($currentTeam))
            ->andReturn(['created' => 0, 'updated' => 0, 'deleted' => 0, 'total' => 0]);
        $mock->shouldReceive('importPlayers')
            ->once()
            ->withArgs(fn (Team $team): bool => $team->is($currentTeam))
            ->andReturn(['created' => 0, 'updated' => 0, 'deleted' => 0, 'total' => 0]);
    });

    $this->artisan('fpb:import', ['team' => 'seniores-masculinos'])->assertSuccessful();
});

test('fpb import can target an archive team by id', function () {
    $archive = Season::ensureForName('2024/2025');
    $archiveTeam = Team::factory()->for(Sport::factory())->for($archive)->create([
        'slug' => 'sub-14-arquivo',
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_9/',
    ]);

    $this->mock(FpbGamesImporter::class, function ($mock) use ($archiveTeam) {
        $mock->shouldReceive('import')
            ->once()
            ->withArgs(fn (Team $team): bool => $team->is($archiveTeam))
            ->andReturn(['created' => 0, 'updated' => 0, 'deleted' => 0, 'total' => 0]);
        $mock->shouldReceive('importPlayers')
            ->once()
            ->withArgs(fn (Team $team): bool => $team->is($archiveTeam))
            ->andReturn(['created' => 0, 'updated' => 0, 'deleted' => 0, 'total' => 0]);
    });

    $this->artisan('fpb:import', ['team' => (string) $archiveTeam->id])->assertSuccessful();
});

test('fpb import rejects an archive-only slug', function () {
    Team::factory()->for(Sport::factory())->for(Season::ensureForName('2024/2025'))->create([
        'slug' => 'sub-14-arquivo',
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_9/',
    ]);

    $this->artisan('fpb:import', ['team' => 'sub-14-arquivo'])
        ->expectsOutput('Equipa "sub-14-arquivo" não encontrada.')
        ->assertFailed();
});
