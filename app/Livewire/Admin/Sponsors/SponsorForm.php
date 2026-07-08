<?php

namespace App\Livewire\Admin\Sponsors;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Sponsor;
use App\Models\Team;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Patrocinador')]
class SponsorForm extends Component
{
    use HandlesImageUpload, WithFileUploads;

    public ?Sponsor $sponsor = null;

    public string $nome = '';

    public string $link = '';

    public string $tipo = Sponsor::TIPO_GERAL;

    /** @var array<int, int> */
    public array $teamIds = [];

    public int $ordem = 0;

    public bool $ativo = true;

    /** @var TemporaryUploadedFile|null */
    public $logo = null;

    public ?string $logoAtual = null;

    /** @var array<string, string> */
    public array $tipos = Sponsor::TIPOS;

    public function mount(?Sponsor $sponsor = null): void
    {
        if ($sponsor && $sponsor->exists) {
            $this->sponsor = $sponsor;
            $this->nome = $sponsor->nome;
            $this->link = $sponsor->link ?? '';
            $this->tipo = $sponsor->tipo;
            $this->teamIds = $sponsor->teams()->pluck('teams.id')->all();
            $this->ordem = $sponsor->ordem ?? 0;
            $this->ativo = $sponsor->ativo;
            $this->logoAtual = $sponsor->logo;
        }
    }

    public function updatedTipo(string $value): void
    {
        if ($value === Sponsor::TIPO_GERAL) {
            $this->teamIds = [];
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $rules = [
            'nome' => ['required', 'string', 'max:255'],
            'link' => ['required', 'url', 'max:255'],
            'tipo' => ['required', 'string', 'in:'.implode(',', array_keys(Sponsor::TIPOS))],
            'ordem' => ['integer', 'min:0'],
            'ativo' => ['boolean'],
            'logo' => [$this->sponsor || $this->logoAtual ? 'nullable' : 'required', 'image', 'max:4096'],
        ];

        if ($this->tipo === Sponsor::TIPO_PRINCIPAL) {
            $rules['teamIds'] = ['required', 'array', 'min:1'];
            $rules['teamIds.*'] = ['integer', 'exists:teams,id'];
        }

        return $rules;
    }

    public function removerLogo(): void
    {
        $this->logo = null;

        if ($this->logoAtual) {
            $this->deleteImage($this->logoAtual);
            $this->logoAtual = null;

            if ($this->sponsor) {
                $this->sponsor->update(['logo' => null]);
            }
        }
    }

    public function save(): mixed
    {
        $validated = $this->validate();

        $data = [
            'nome' => $validated['nome'],
            'link' => $validated['link'],
            'tipo' => $validated['tipo'],
            'ordem' => $validated['ordem'],
            'ativo' => $validated['ativo'],
        ];

        if ($this->logo) {
            $this->deleteImage($this->logoAtual);
            $data['logo'] = $this->storeImage($this->logo, 'patrocinadores');
        }

        if ($this->sponsor) {
            $this->sponsor->update($data);
            $sponsor = $this->sponsor;
        } else {
            $sponsor = Sponsor::create($data);
        }

        $sponsor->teams()->sync($this->tipo === Sponsor::TIPO_PRINCIPAL ? $this->teamIds : []);

        Flux::toast(variant: 'success', text: 'Patrocinador guardado.');

        return $this->redirectRoute('admin.sponsors.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.sponsors.form', [
            'equipas' => Team::query()->orderBy('ordem')->orderBy('nome')->get(),
        ]);
    }
}
