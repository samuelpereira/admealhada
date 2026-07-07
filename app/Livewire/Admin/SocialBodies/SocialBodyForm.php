<?php

namespace App\Livewire\Admin\SocialBodies;

use App\Models\SocialBody;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Órgão social')]
class SocialBodyForm extends Component
{
    public ?SocialBody $socialBody = null;

    public string $categoria = 'direcao';

    public string $cargo = '';

    public string $nome = '';

    public int $ordem = 0;

    public bool $ativo = true;

    /** @var array<string, string> */
    public array $categorias = SocialBody::CATEGORIAS;

    public function mount(?SocialBody $socialBody = null): void
    {
        if ($socialBody && $socialBody->exists) {
            $this->socialBody = $socialBody;
            $this->categoria = $socialBody->categoria;
            $this->cargo = $socialBody->cargo;
            $this->nome = $socialBody->nome;
            $this->ordem = $socialBody->ordem ?? 0;
            $this->ativo = $socialBody->ativo;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'categoria' => ['required', 'string', 'in:'.implode(',', array_keys(SocialBody::CATEGORIAS))],
            'cargo' => ['required', 'string', 'max:255'],
            'nome' => ['required', 'string', 'max:255'],
            'ordem' => ['integer', 'min:0'],
            'ativo' => ['boolean'],
        ];
    }

    public function save(): mixed
    {
        $validated = $this->validate();

        if ($this->socialBody) {
            $this->socialBody->update($validated);
        } else {
            SocialBody::create($validated);
        }

        Flux::toast(variant: 'success', text: 'Membro guardado.');

        return $this->redirectRoute('admin.social-bodies.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.social-bodies.form');
    }
}
