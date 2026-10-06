<?php

use App\Models\Game;
use App\Models\Player;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Services\Fpb\FpbGamesImporter;
use Illuminate\Support\Facades\Http;

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

test('reimporting a finished game keeps the kickoff time when the fpb page has no hour', function () {
    Http::fake([
        'https://fpb.test/equipa' => Http::response(<<<'HTML'
<h3 class="date">3 OUT 2026</h3>
<a href="/ficha-de-jogo?internalID=100">
<span class="fullName">AD Mealhada</span>
<span class="fullName">Ovarense</span>
<span class="results_text">72</span>
<span class="results_text">65</span>
<div class="location-wrapper"><b>Pavilhão</b></div>
<div class="competition"><span>CN | Proliga</span></div>
</a>
<a href="/ficha-de-jogo?internalID=101">
<span class="fullName">AD Mealhada</span>
<span class="fullName">Illiabum</span>
<div class="hour"><h3>16H00</h3></div>
</a>
HTML),
    ]);

    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create([
        'fpb_url' => 'https://fpb.test/equipa',
    ]);
    $finished = Game::factory()->for($team)->create([
        'fpb_id' => '100',
        'data_hora' => '2026-10-03 18:30:00',
        'estado' => 'agendado',
    ]);

    app(FpbGamesImporter::class)->import($team, prune: false);

    expect($finished->fresh()->data_hora?->format('Y-m-d H:i'))->toBe('2026-10-03 18:30')
        ->and($finished->fresh()->estado)->toBe('terminado')
        ->and($finished->fresh()->pontos_casa)->toBe(72)
        ->and(Game::query()->where('fpb_id', '101')->first()?->data_hora?->format('H:i'))->toBe('16:00');
});

test('an fpb import capitalizes player names and keeps club names', function () {
    Http::fake([
        'https://fpb.test/equipa' => Http::response(<<<'HTML'
<h3 class="date">3 OUT 2026</h3>
<a href="/ficha-de-jogo?internalID=100">
<span class="fullName">AD MEALHADA</span>
<span class="fullName">OVAR CLUBE</span>
</a>
<a href="/ficha-de-jogo?internalID=101">
<span class="fullName">ILLIABUM</span>
<span class="fullName">AD MEALHADA</span>
</a>
<div class="roster__player">
<a href="/atletas/4242"></a>
<h2 class="header__heading">JOÃO DA SILVA</h2>
<div class="header_jnumber">7</div>
</div>
HTML),
    ]);

    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create([
        'fpb_url' => 'https://fpb.test/equipa',
    ]);

    $importer = app(FpbGamesImporter::class);
    $importer->import($team, prune: false);
    $importer->importPlayers($team, prune: false);

    expect(Game::query()->where('fpb_id', '100')->first()?->adversario)->toBe('OVAR CLUBE')
        ->and(Game::query()->where('fpb_id', '101')->first()?->adversario)->toBe('ILLIABUM')
        ->and(Player::query()->where('fpb_id', '4242')->first()?->nome)->toBe('João Da Silva');
});
