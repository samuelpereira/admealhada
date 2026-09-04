<?php

namespace App\Livewire\Site;

use App\Models\Album;
use App\Models\Game;
use App\Models\News;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('Início')]
class Home extends Component
{
    public function render(): View
    {
        return view('livewire.site.home', [
            'proximosJogos' => Game::query()->with('team')->proximos()->take(4)->get(),
            'ultimosResultados' => Game::query()->with('team')->resultados()->take(4)->get(),
            'noticias' => News::query()->publicadas()->take(3)->get(),
            'destaque' => News::query()->publicadas()->where('destaque', true)->first()
                ?? News::query()->publicadas()->first(),
            'equipas' => Team::query()->orderBy('ordem')->get(),
            'modalidades' => Sport::query()->where('ativo', true)->orderBy('nome')->get(),
            'albuns' => Album::query()->withCount('photos')->latest('data')->take(4)->get(),
            'totalEquipas' => Team::query()->count(),
            'totalJogadores' => Player::query()->where('papel', 'jogador')->count(),
            'totalPatrocinadores' => Sponsor::query()->where('ativo', true)->count(),
        ]);
    }
}
