<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Utilizador')]
class UserForm extends Component
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isAdmin = true;

    public function mount(?User $user = null): void
    {
        if ($user && $user->exists) {
            $this->user = $user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->isAdmin = (bool) $user->is_admin;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'password' => [$this->user ? 'nullable' : 'required', 'string', Password::default(), 'confirmed'],
            'isAdmin' => ['boolean'],
        ];
    }

    public function save(): mixed
    {
        if ($this->user?->is(Auth::user()) && ! $this->isAdmin) {
            $this->addError('isAdmin', 'Não podes retirar o teu próprio acesso de administrador.');

            return null;
        }

        $validated = $this->validate();

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $validated['isAdmin'],
        ];

        if ($validated['password'] !== '') {
            $data['password'] = $validated['password'];
        }

        if ($this->user) {
            $this->user->update($data);
        } else {
            User::query()->create($data);
        }

        Flux::toast(variant: 'success', text: 'Utilizador guardado.');

        return $this->redirectRoute('admin.users.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.users.user-form');
    }
}
