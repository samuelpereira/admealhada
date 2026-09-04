<?php

use App\Livewire\Admin\Teams\TeamForm;
use App\Livewire\Admin\Teams\TeamsIndex;
use App\Models\Game;
use App\Models\Player;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('duplicating a team copies identity into the next season without roster games or fpb url', function () {
    Storage::fake('public');

    $season = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();
    Storage::disk('public')->put('equipas/plantel.jpg', 'image');

    $team = Team::factory()->for($sport)->for($season)->create([
        'nome' => 'Séniores Masculinos',
        'slug' => 'seniores-masculinos',
        'escalao' => 'Sénior',
        'genero' => 'M',
        'ordem' => 3,
        'descricao' => 'Equipa principal',
        'fpb_url' => 'https://www.fpb.pt/equipa/equipa_1/',
        'competicao' => 'CN2',
        'foto' => 'equipas/plantel.jpg',
    ]);

    Player::factory()->for($team)->create();
    Game::factory()->for($team)->create();

    $copy = $team->duplicateIntoNextSeason();

    expect($copy->season->name)->toBe('2025/2026')
        ->and($copy->nome)->toBe('Séniores Masculinos')
        ->and($copy->slug)->toBe('seniores-masculinos')
        ->and($copy->escalao)->toBe('Sénior')
        ->and($copy->genero)->toBe('M')
        ->and($copy->ordem)->toBe(3)
        ->and($copy->descricao)->toBe('Equipa principal')
        ->and($copy->sport_id)->toBe($sport->id)
        ->and($copy->fpb_url)->toBeNull()
        ->and($copy->players()->count())->toBe(0)
        ->and($copy->games()->count())->toBe(0)
        ->and($copy->foto)->not->toBe('equipas/plantel.jpg')
        ->and($team->fresh()->players()->count())->toBe(1)
        ->and($team->fresh()->games()->count())->toBe(1);

    Storage::disk('public')->assertExists($copy->foto);
    Storage::disk('public')->assertExists('equipas/plantel.jpg');
});

test('duplicating a team fails when the next season already has that slug', function () {
    $season = Season::ensureForName('2024/2025');
    $next = $season->next();
    $sport = Sport::factory()->create();

    $team = Team::factory()->for($sport)->for($season)->create(['slug' => 'seniores-masculinos']);
    Team::factory()->for($sport)->for($next)->create(['slug' => 'seniores-masculinos']);

    $team->duplicateIntoNextSeason();
})->throws(RuntimeException::class, 'Já existe uma equipa com este slug na época 2025/2026.');

test('admin team index lists the current season by default', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    Team::factory()->for($sport)->for($current)->create(['nome' => 'Equipa actual']);
    Team::factory()->for($sport)->for($archive)->create(['nome' => 'Equipa arquivo']);

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(TeamsIndex::class)
        ->assertSee('Equipa actual')
        ->assertDontSee('Equipa arquivo')
        ->set('epoca', $archive->slug)
        ->assertSee('Equipa arquivo')
        ->assertDontSee('Equipa actual');
});

test('admin can duplicate a team into the next season', function () {
    $season = Season::ensureForName('2024/2025');
    $team = Team::factory()->for(Sport::factory())->for($season)->create([
        'nome' => 'Sub-14 arquivo',
        'slug' => 'sub-14-arquivo',
    ]);

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(TeamsIndex::class)
        ->set('epoca', $season->slug)
        ->call('duplicate', $team->id);

    $copy = Team::query()->where('slug', 'sub-14-arquivo')->where('season_id', $season->next()->id)->first();

    expect($copy)->not->toBeNull()
        ->and($copy?->fpb_url)->toBeNull();
});

test('creating a team keeps the same slug in another season', function () {
    $current = Season::ensureCurrent();
    $archive = Season::ensureForName('2024/2025');
    $sport = Sport::factory()->create();

    Team::factory()->for($sport)->for($archive)->create([
        'nome' => 'Séniores Masculinos',
        'slug' => 'seniores-masculinos',
    ]);

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(TeamForm::class)
        ->set('season_id', $current->id)
        ->set('sport_id', $sport->id)
        ->set('nome', 'Séniores Masculinos')
        ->set('escalao', 'Sénior')
        ->set('genero', 'M')
        ->call('save');

    expect(Team::query()->where('slug', 'seniores-masculinos')->count())->toBe(2)
        ->and(Team::query()->currentSeason()->where('slug', 'seniores-masculinos')->count())->toBe(1);
});
