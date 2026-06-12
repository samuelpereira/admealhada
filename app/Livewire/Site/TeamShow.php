<?php

namespace App\Livewire\Site;

use App\Models\Team;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.site')]
class TeamShow extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->team = $team->load(['sport', 'players']);
    }

    public function render()
    {
        return View::make('livewire.site.team-show', [
            'jogadores' => $this->team->players->where('papel', 'jogador'),
            'staff' => $this->team->players->where('papel', '!=', 'jogador'),
            'proximosJogos' => $this->team->games()->proximos()->take(5)->get(),
            'resultados' => $this->team->games()->resultados()->take(8)->get(),
        ])->title($this->team->nome.' - '.config('app.name'));
    }
}
