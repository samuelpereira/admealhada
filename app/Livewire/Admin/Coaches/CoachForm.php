<?php

namespace App\Livewire\Admin\Coaches;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Coach;
use App\Models\Team;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('components.layouts.admin')]
#[Title('Treinador')]
class CoachForm extends Component
{
    use HandlesImageUpload, WithFileUploads;

    public ?Coach $coach = null;

    public string $nome = '';

    public ?string $dataNascimento = null;

    public string $nacionalidade = '';

    public string $grau = '';

    /** @var array<int, int> */
    public array $teamIds = [];

    /** @var TemporaryUploadedFile|null */
    public $foto = null;

    public ?string $fotoAtual = null;

    public function mount(?Coach $coach = null): void
    {
        if ($coach && $coach->exists) {
            $this->coach = $coach;
            $this->nome = $coach->nome;
            $this->dataNascimento = $coach->data_nascimento?->format('Y-m-d');
            $this->nacionalidade = $coach->nacionalidade ?? '';
            $this->grau = $coach->grau ?? '';
            $this->teamIds = $coach->teams()->pluck('teams.id')->all();
            $this->fotoAtual = $coach->foto;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'dataNascimento' => ['nullable', 'date', 'before:today'],
            'nacionalidade' => ['nullable', 'string', 'max:120'],
            'grau' => ['nullable', 'string', 'max:120'],
            'teamIds' => ['array'],
            'teamIds.*' => ['integer', 'exists:teams,id'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function removerFoto(): void
    {
        $this->foto = null;

        if ($this->fotoAtual) {
            $this->deleteImage($this->fotoAtual);
            $this->fotoAtual = null;

            if ($this->coach) {
                $this->coach->update(['foto' => null]);
            }
        }
    }

    public function save(): mixed
    {
        $validated = $this->validate();

        $data = [
            'nome' => $validated['nome'],
            'data_nascimento' => $validated['dataNascimento'] ?? null,
            'nacionalidade' => $validated['nacionalidade'] !== '' ? $validated['nacionalidade'] : null,
            'grau' => $validated['grau'] !== '' ? $validated['grau'] : null,
        ];

        if ($this->foto) {
            $this->deleteImage($this->fotoAtual);
            $data['foto'] = $this->storeImage($this->foto, 'treinadores');
        }

        if ($this->coach) {
            $this->coach->update($data);
            $coach = $this->coach;
        } else {
            $coach = Coach::create($data);
        }

        $coach->teams()->sync($this->teamIds);

        Flux::toast(variant: 'success', text: 'Treinador guardado.');

        return $this->redirectRoute('admin.coaches.index', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.coaches.coach-form', [
            'equipas' => Team::query()->forAdminLists()->get(),
        ]);
    }
}
