<?php

use App\Livewire\Admin\Users\UserForm;
use App\Livewire\Admin\Users\UsersIndex;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

test('guests are redirected away from users admin', function () {
    $this->get(route('admin.users.index'))
        ->assertRedirect(route('login'));
});

test('non-admin users cannot manage users', function () {
    $this->actingAs(User::factory()->create(['is_admin' => false]));

    $this->get(route('admin.users.index'))
        ->assertForbidden();
});

test('an admin can create a user', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(UserForm::class)
        ->set('name', 'Rita Costa')
        ->set('email', 'rita@example.com')
        ->set('password', 'secret-password')
        ->set('password_confirmation', 'secret-password')
        ->set('isAdmin', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'rita@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->is_admin)->toBeTrue()
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});

test('an admin can rename a user without changing the password', function () {
    $this->actingAs(User::factory()->admin()->create());

    $user = User::factory()->create([
        'name' => 'Nome Antigo',
        'password' => 'secret-password',
    ]);

    Livewire::test(UserForm::class, ['user' => $user])
        ->set('name', 'Nome Novo')
        ->set('password', '')
        ->set('password_confirmation', '')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $user->refresh();

    expect($user->name)->toBe('Nome Novo')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});

test('an admin can delete another user but not their own account', function () {
    $admin = User::factory()->admin()->create();
    $other = User::factory()->create();

    $this->actingAs($admin);

    Livewire::test(UsersIndex::class)
        ->call('delete', $admin);

    expect(User::query()->find($admin->id))->not->toBeNull();

    Livewire::test(UsersIndex::class)
        ->call('delete', $other);

    expect(User::query()->find($other->id))->toBeNull();
});
