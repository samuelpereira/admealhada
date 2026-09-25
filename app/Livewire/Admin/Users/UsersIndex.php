<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Utilizadores')]
class UsersIndex extends Component
{
    public function delete(User $user): void
    {
        if ($user->is(Auth::user())) {
            Flux::toast(variant: 'danger', text: 'Não podes eliminar a tua própria conta.');

            return;
        }

        $user->delete();

        Flux::toast(variant: 'success', text: 'Utilizador eliminado.');
    }

    public function render(): View
    {
        return view('livewire.admin.users.users-index', [
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }
}
