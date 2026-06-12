<?php

namespace App\Livewire\Admin\Sponsors;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Sponsor;
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

    public int $ordem = 0;

    public bool $ativo = true;

    /** @var TemporaryUploadedFile|null */
    public $logo = null;

    public ?string $logoAtual = null;

    public function mount(?Sponsor $sponsor = null): void
    {
        if ($sponsor && $sponsor->exists) {
            $this->sponsor = $sponsor;
            $this->nome = $sponsor->nome;
            $this->link = $sponsor->link ?? '';
            $this->ordem = $sponsor->ordem ?? 0;
            $this->ativo = $sponsor->ativo;
            $this->logoAtual = $sponsor->logo;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'link' => ['required', 'url', 'max:255'],
            'ordem' => ['integer', 'min:0'],
            'ativo' => ['boolean'],
            'logo' => [$this->sponsor || $this->logoAtual ? 'nullable' : 'required', 'image', 'max:4096'],
        ];
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
            'ordem' => $validated['ordem'],
            'ativo' => $validated['ativo'],
        ];

        if ($this->logo) {
            $this->deleteImage($this->logoAtual);
            $data['logo'] = $this->storeImage($this->logo, 'patrocinadores');
        }

        if ($this->sponsor) {
            $this->sponsor->update($data);
        } else {
            Sponsor::create($data);
        }

        Flux::toast(variant: 'success', text: 'Patrocinador guardado.');

        return $this->redirectRoute('admin.sponsors.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.sponsors.form');
    }
}
