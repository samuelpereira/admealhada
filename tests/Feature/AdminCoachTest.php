<?php

use App\Livewire\Admin\Coaches\CoachesIndex;
use App\Livewire\Admin\Coaches\CoachForm;
use App\Models\Coach;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected away from coaches', function () {
    $this->get(route('admin.coaches.index'))
        ->assertRedirect(route('login'));
});

test('an admin can assign one coach to several teams', function () {
    $this->actingAs(User::factory()->admin()->create());

    $season = Season::ensureCurrent();
    $sport = Sport::factory()->create();
    $seniores = Team::factory()->for($sport)->for($season)->create(['nome' => 'Seniores']);
    $sub16 = Team::factory()->for($sport)->for($season)->create(['nome' => 'Sub-16']);

    Livewire::test(CoachForm::class)
        ->set('nome', 'Rui Costa')
        ->set('dataNascimento', '1980-04-12')
        ->set('nacionalidade', 'Portugal')
        ->set('grau', 'Grau II')
        ->set('teamIds', [$seniores->id, $sub16->id])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.coaches.index'));

    $coach = Coach::query()->sole();

    expect($coach->nome)->toBe('Rui Costa')
        ->and($coach->nacionalidade)->toBe('Portugal')
        ->and($coach->data_nascimento?->toDateString())->toBe('1980-04-12')
        ->and($coach->teams)->toHaveCount(2);

    $this->get($seniores->publicShowUrl())
        ->assertOk()
        ->assertSeeInOrder(['Plantel', 'Treinadores', 'Rui Costa'])
        ->assertSee('Grau II');

    $this->get($sub16->publicShowUrl())
        ->assertOk()
        ->assertSee('Rui Costa');
});

test('a team page hides coaches from other teams', function () {
    $season = Season::ensureCurrent();
    $sport = Sport::factory()->create();
    $team = Team::factory()->for($sport)->for($season)->create();
    $other = Team::factory()->for($sport)->for($season)->create();
    $coach = Coach::factory()->create(['nome' => 'Ana Lopes']);
    $other->coaches()->attach($coach);

    $this->get($team->publicShowUrl())
        ->assertOk()
        ->assertDontSee('Ana Lopes')
        ->assertDontSee('Treinadores');
});

test('an admin can delete a coach', function () {
    $this->actingAs(User::factory()->admin()->create());

    $coach = Coach::factory()->create();

    Livewire::test(CoachesIndex::class)
        ->call('delete', $coach);

    expect(Coach::query()->find($coach->id))->toBeNull();
});

test('coaches on a team page follow the order they were assigned', function () {
    $season = Season::ensureCurrent();
    $sport = Sport::factory()->create();
    $team = Team::factory()->for($sport)->for($season)->create();
    $later = Coach::factory()->create(['nome' => 'Ana Lopes']);
    $earlier = Coach::factory()->create(['nome' => 'Zé Mendes']);

    $team->coaches()->attach($earlier, ['created_at' => '2026-01-01 10:00:00', 'updated_at' => '2026-01-01 10:00:00']);
    $team->coaches()->attach($later, ['created_at' => '2026-06-01 10:00:00', 'updated_at' => '2026-06-01 10:00:00']);

    $this->get($team->publicShowUrl())
        ->assertOk()
        ->assertSeeInOrder(['Zé Mendes', 'Ana Lopes']);
});
