<?php

namespace App\Livewire\Admin\Teams;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Season;
use App\Models\Team;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use RuntimeException;
use Throwable;

#[Layout('components.layouts.admin')]
#[Title('Equipas')]
class TeamsIndex extends Component
{
    use HandlesImageUpload;

    #[Url(as: 'epoca')]
    public string $epoca = '';

    public function mount(): void
    {
        if ($this->epoca === '') {
            $current = Season::current();
            $this->epoca = $current === null ? '' : $current->slug;
        }
    }

    public function delete(int $teamId): void
    {
        $team = Team::query()->findOrFail($teamId);

        $this->deleteImage($team->foto);
        $team->delete();

        Flux::toast(variant: 'success', text: 'Equipa eliminada.');
    }

    public function duplicate(int $teamId): mixed
    {
        $team = Team::query()->findOrFail($teamId);

        try {
            $copy = $team->duplicateIntoNextSeason();
        } catch (RuntimeException $e) {
            Flux::toast(variant: 'danger', text: $e->getMessage());

            return null;
        } catch (Throwable $e) {
            Flux::toast(variant: 'danger', text: 'Não foi possível duplicar a equipa.');

            return null;
        }

        Flux::toast(variant: 'success', text: 'Equipa duplicada para '.$copy->season->name.'.');

        return $this->redirectRoute('admin.teams.edit', $copy, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.teams.index', [
            'teams' => Team::query()
                ->with('sport')
                ->withCount('players')
                ->when($this->epoca !== '', fn ($query) => $query->whereHas(
                    'season',
                    fn ($seasonQuery) => $seasonQuery->where('slug', $this->epoca),
                ))
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get(),
            'epocas' => Season::query()->orderByDesc('starts_at')->get(),
        ]);
    }
}
