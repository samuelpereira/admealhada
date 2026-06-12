<?php

namespace App\Livewire\Admin\Sports;

use App\Livewire\Concerns\GeneratesSlugs;
use App\Models\Sport;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Modalidade')]
class SportForm extends Component
{
    use GeneratesSlugs;

    public ?Sport $sport = null;

    public string $nome = '';

    public string $descricao = '';

    public bool $ativo = true;

    public function mount(?Sport $sport = null): void
    {
        if ($sport && $sport->exists) {
            $this->sport = $sport;
            $this->nome = $sport->nome;
            $this->descricao = $sport->descricao ?? '';
            $this->ativo = $sport->ativo;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'ativo' => ['boolean'],
        ];
    }

    public function save(): mixed
    {
        $data = $this->validate();
        $data['slug'] = $this->uniqueSlug($this->nome, 'sports', $this->sport?->id);

        if ($this->sport) {
            $this->sport->update($data);
        } else {
            Sport::create($data);
        }

        Flux::toast(variant: 'success', text: 'Modalidade guardada.');

        return $this->redirectRoute('admin.sports.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.sports.form');
    }
}
