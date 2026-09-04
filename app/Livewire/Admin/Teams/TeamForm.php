<?php

namespace App\Livewire\Admin\Teams;

use App\Livewire\Concerns\GeneratesSlugs;
use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Season;
use App\Models\Sport;
use App\Models\Team;
use App\Services\Fpb\FpbGamesImporter;
use Flux\Flux;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

#[Layout('components.layouts.admin')]
#[Title('Equipa')]
class TeamForm extends Component
{
    use GeneratesSlugs, HandlesImageUpload, WithFileUploads;

    public ?Team $team = null;

    public ?int $season_id = null;

    public ?int $sport_id = null;

    public string $nome = '';

    public string $escalao = '';

    public string $genero = 'M';

    public string $competicao = '';

    public string $fpb_url = '';

    public string $descricao = '';

    public int $ordem = 0;

    /** @var TemporaryUploadedFile|null */
    public $foto = null;

    public ?string $fotoAtual = null;

    public function mount(?Team $team = null): void
    {
        if ($team && $team->exists) {
            $this->team = $team;
            $this->season_id = $team->season_id;
            $this->sport_id = $team->sport_id;
            $this->nome = $team->nome;
            $this->escalao = $team->escalao ?? '';
            $this->genero = $team->genero ?? 'M';
            $this->competicao = $team->competicao ?? '';
            $this->fpb_url = $team->fpb_url ?? '';
            $this->descricao = $team->descricao ?? '';
            $this->ordem = $team->ordem ?? 0;
            $this->fotoAtual = $team->foto;
        } else {
            $this->season_id = Season::current()?->id;
            $this->sport_id = Sport::query()->where('ativo', true)->value('id') ?? Sport::query()->value('id');
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'season_id' => ['required', 'exists:seasons,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'nome' => ['required', 'string', 'max:255'],
            'escalao' => ['required', 'string', 'max:50'],
            'genero' => ['required', 'in:M,F,X'],
            'competicao' => ['nullable', 'string', 'max:255'],
            'fpb_url' => ['nullable', 'url', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'ordem' => ['integer', 'min:0'],
            'foto' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function removerFoto(): void
    {
        $this->foto = null;

        if ($this->fotoAtual) {
            $this->deleteImage($this->fotoAtual);
            $this->fotoAtual = null;

            if ($this->team) {
                $this->team->update(['foto' => null]);
            }
        }
    }

    public function importarFpb(FpbGamesImporter $importer): void
    {
        if (! $this->team) {
            return;
        }

        $this->validate(['fpb_url' => ['required', 'url', 'max:255']]);

        if ($this->team->fpb_url !== $this->fpb_url) {
            $this->team->update(['fpb_url' => $this->fpb_url]);
        }

        try {
            $stats = $importer->import($this->team);

            Flux::toast(
                variant: 'success',
                text: "Jogos: {$stats['total']} · {$stats['created']} novos · {$stats['updated']} atualizados · {$stats['deleted']} removidos.",
            );
        } catch (Throwable $e) {
            Flux::toast(variant: 'danger', text: 'Falha na importação: '.$e->getMessage());
        }
    }

    public function importarPlantelFpb(FpbGamesImporter $importer): void
    {
        if (! $this->team) {
            return;
        }

        $this->validate(['fpb_url' => ['required', 'url', 'max:255']]);

        if ($this->team->fpb_url !== $this->fpb_url) {
            $this->team->update(['fpb_url' => $this->fpb_url]);
        }

        try {
            $stats = $importer->importPlayers($this->team);

            Flux::toast(
                variant: 'success',
                text: "Plantel: {$stats['total']} · {$stats['created']} novos · {$stats['updated']} atualizados · {$stats['deleted']} removidos.",
            );
        } catch (Throwable $e) {
            Flux::toast(variant: 'danger', text: 'Falha na importação: '.$e->getMessage());
        }
    }

    public function save(): mixed
    {
        $validated = $this->validate();

        $data = Arr::except($validated, ['foto']);
        $data['slug'] = $this->uniqueSlug($this->nome, 'teams', $this->team?->id, 'slug', [
            'season_id' => $this->season_id,
        ]);

        if ($this->foto) {
            $this->deleteImage($this->fotoAtual);
            $data['foto'] = $this->storeImage($this->foto, 'equipas');
        }

        if ($this->team) {
            $this->team->update($data);
            $this->team->refresh();

            if ($this->foto) {
                $this->fotoAtual = $this->team->foto;
                $this->foto = null;
            }

            Flux::toast(variant: 'success', text: 'Equipa guardada.');

            return null;
        }

        $team = Team::create($data);

        Flux::toast(variant: 'success', text: 'Equipa guardada.');

        return $this->redirectRoute('admin.teams.edit', $team, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.teams.form', [
            'sports' => Sport::orderBy('nome')->get(),
            'epocas' => Season::query()->orderByDesc('starts_at')->get(),
        ]);
    }
}
