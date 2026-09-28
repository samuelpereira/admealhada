<?php

namespace App\Livewire\Admin;

use App\Models\Album;
use App\Models\Game;
use App\Models\News;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Backoffice')]
class Dashboard extends Component
{
    public function render(): View
    {
        return view('livewire.admin.dashboard', [
            'cards' => [
                ['label' => 'Notícias', 'value' => News::count(), 'icon' => 'newspaper', 'route' => 'admin.news.index'],
                ['label' => 'Equipas', 'value' => Team::query()->currentSeason()->count(), 'icon' => 'user-group', 'route' => 'admin.teams.index'],
                ['label' => 'Jogadores', 'value' => Player::countPeopleInCurrentSeason(), 'icon' => 'identification', 'route' => 'admin.players.index'],
                ['label' => 'Jogos', 'value' => Game::query()->whereHas('team', fn ($query) => $query->currentSeason())->count(), 'icon' => 'calendar-days', 'route' => 'admin.games.index'],
                ['label' => 'Galerias', 'value' => Album::count(), 'icon' => 'photo', 'route' => 'admin.albums.index'],
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
