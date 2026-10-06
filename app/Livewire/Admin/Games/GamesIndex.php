<?php

namespace App\Livewire\Admin\Games;

use App\Models\Game;
use App\Models\Season;
use App\Models\Team;
use App\Support\NameFilter;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Jogos')]
class GamesIndex extends Component
{
    use WithPagination;

    #[Url(as: 'epoca')]
    public string $epoca = '';

    #[Url]
    public string $team = '';

    #[Url]
    public string $estado = '';

    #[Url(except: '')]
    public string $pesquisa = '';

    public function mount(): void
    {
        if ($this->epoca === '') {
            $current = Season::current();
            $this->epoca = $current === null ? '' : $current->slug;
        }

        $this->forgetTeamOutsideSeason();
    }

    public function updating(string $name): void
    {
        if (in_array($name, ['team', 'estado', 'epoca', 'pesquisa'], true)) {
            $this->resetPage();
        }
    }

    public function updatedEpoca(): void
    {
        $this->forgetTeamOutsideSeason();
    }

    public function delete(Game $game): void
    {
        $game->delete();

        Flux::toast(variant: 'success', text: 'Jogo eliminado.');
    }

    public function render(): View
    {
        $query = Game::query()
            ->with('team')
            ->when($this->epoca !== '', fn ($query) => $query->whereHas(
                'team',
                fn ($teamQuery) => $teamQuery->whereHas(
                    'season',
                    fn ($seasonQuery) => $seasonQuery->where('slug', $this->epoca),
                ),
            ))
            ->when($this->team !== '', fn ($query) => $query->where('team_id', $this->team))
            ->when($this->estado !== '', fn ($query) => $query->where('estado', $this->estado))
            ->orderByDesc('data_hora');

        if (trim($this->pesquisa) === '') {
            $games = $query->paginate(15);
        } else {
            $filtered = $query->get()
                ->filter(fn (Game $game): bool => NameFilter::matches($game->adversario, $this->pesquisa)
                    || NameFilter::matches($game->team->nome, $this->pesquisa))
                ->values();

            $games = new LengthAwarePaginator(
                $filtered->forPage($this->getPage(), 15),
                $filtered->count(),
                15,
                $this->getPage(),
                ['path' => Paginator::resolveCurrentPath()],
            );
        }

        return view('livewire.admin.games.index', [
            'games' => $games,
            'teams' => $this->teamsForSeason(),
            'epocas' => Season::query()->orderByDesc('starts_at')->get(),
        ]);
    }

    /**
     * @return Collection<int, Team>
     */
    private function teamsForSeason(): Collection
    {
        return Team::query()
            ->forAdminLists()
            ->when($this->epoca !== '', fn ($query) => $query->whereHas(
                'season',
                fn ($seasonQuery) => $seasonQuery->where('slug', $this->epoca),
            ))
            ->get();
    }

    private function forgetTeamOutsideSeason(): void
    {
        if ($this->team === '') {
            return;
        }

        $belongsToSeason = $this->teamsForSeason()->contains(
            fn (Team $team): bool => (string) $team->id === $this->team,
        );

        if (! $belongsToSeason) {
            $this->team = '';
        }
    }
}
