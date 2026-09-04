<?php

use App\Livewire\Admin\Seasons\SeasonForm;
use App\Livewire\Admin\Seasons\SeasonsIndex;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected away from season admin', function () {
    $this->get(route('admin.seasons.index'))
        ->assertRedirect(route('login'));
});

test('non-admin users cannot visit season admin', function () {
    $this->actingAs(User::factory()->create(['is_admin' => false]));

    $this->get(route('admin.seasons.index'))
        ->assertForbidden();
});

test('admin sidebar lists epochs under modalidades without dropping other items', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard')
        ->assertSee('Modalidades')
        ->assertSee('Épocas')
        ->assertSee(route('admin.seasons.index'), false)
        ->assertSee('Equipas')
        ->assertSee('Jogadores')
        ->assertSee('Jogos');
});

test('admins can view seasons and see them under modalidades', function () {
    $this->actingAs(User::factory()->admin()->create());

    $this->get(route('admin.seasons.index'))
        ->assertOk()
        ->assertSee('Épocas')
        ->assertSee(route('admin.seasons.index'), false)
        ->assertSee('Modalidades');
});

test('admins can create a season from a year name', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonForm::class)
        ->set('name', '2036/2037')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.seasons.index'));

    $season = Season::query()->where('slug', '2036-2037')->first();

    expect($season)->not->toBeNull()
        ->and($season?->name)->toBe('2036/2037')
        ->and($season?->starts_at?->toDateString())->toBe('2036-09-01')
        ->and($season?->ends_at?->toDateString())->toBe('2037-08-31')
        ->and($season?->is_current)->toBeFalse();
});

test('admins can rename a season', function () {
    $season = Season::ensureForName('2036/2037');

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonForm::class, ['season' => $season])
        ->set('name', '2037/2038')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.seasons.index'));

    expect($season->fresh()->name)->toBe('2037/2038')
        ->and($season->fresh()->slug)->toBe('2037-2038')
        ->and($season->fresh()->starts_at?->toDateString())->toBe('2037-09-01');
});

test('season names must be consecutive years', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonForm::class)
        ->set('name', '2025/2027')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('season names must be unique', function () {
    Season::ensureForName('2036/2037');

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonForm::class)
        ->set('name', '2036/2037')
        ->call('save')
        ->assertHasErrors(['name']);
});

test('admins can mark another season as current', function () {
    $current = Season::ensureCurrent();
    $other = Season::ensureForName('2036/2037');

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonsIndex::class)
        ->call('markCurrent', $other->id);

    expect($other->fresh()->is_current)->toBeTrue()
        ->and($current->fresh()->is_current)->toBeFalse()
        ->and(Season::query()->current()->count())->toBe(1);
});

test('the current season cannot be deleted', function () {
    $current = Season::ensureCurrent();

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonsIndex::class)
        ->call('delete', $current->id);

    expect(Season::query()->find($current->id))->not->toBeNull();
});

test('a season with teams cannot be deleted', function () {
    $season = Season::ensureForName('2036/2037');
    Team::factory()->for(Sport::factory())->for($season)->create();

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonsIndex::class)
        ->call('delete', $season->id);

    expect(Season::query()->find($season->id))->not->toBeNull();
});

test('an empty past season can be deleted', function () {
    $season = Season::ensureForName('2036/2037');

    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(SeasonsIndex::class)
        ->call('delete', $season->id);

    expect(Season::query()->find($season->id))->toBeNull();
});
