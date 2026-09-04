<?php

namespace App\Livewire\Admin\Games;

use App\Models\Game;
use App\Models\Team;
use Flux\Flux;
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

    #[Url]
    public string $team = '';

    #[Url]
    public string $estado = '';

    public function updating(string $name): void
    {
        if (in_array($name, ['team', 'estado'], true)) {
            $this->resetPage();
        }
    }

    public function delete(Game $game): void
    {
        $game->delete();

        Flux::toast(variant: 'success', text: 'Jogo eliminado.');
    }

    public function render(): View
    {
        $games = Game::query()
            ->with('team')
            ->when($this->team !== '', fn ($query) => $query->where('team_id', $this->team))
            ->when($this->estado !== '', fn ($query) => $query->where('estado', $this->estado))
            ->orderByDesc('data_hora')
            ->paginate(15);

        return view('livewire.admin.games.index', [
            'games' => $games,
            'teams' => Team::query()->forAdminLists()->get(),
        ]);
    }
}
