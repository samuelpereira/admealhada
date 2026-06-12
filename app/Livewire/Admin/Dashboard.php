<?php

namespace App\Livewire\Admin;

use App\Models\Album;
use App\Models\Game;
use App\Models\News;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Backoffice')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totals' => [
                'news' => News::count(),
                'teams' => Team::count(),
                'players' => Player::count(),
                'games' => Game::count(),
                'albums' => Album::count(),
            ],
            'proximosJogos' => Game::query()->with('team')->proximos()->take(5)->get(),
            'rascunhos' => News::query()
                ->where(fn ($q) => $q->whereNull('publicado_em')->orWhere('publicado_em', '>', Carbon::now()))
                ->latest('updated_at')
                ->take(5)
                ->get(),
        ]);
    }
}
