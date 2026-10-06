<?php

namespace App\Livewire\Admin;

use App\Models\Album;
use App\Models\Game;
use App\Models\News;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
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
            'aniversarios' => $this->aniversariosDoMes(),
            'mesAniversarios' => $this->nomeDoMesAtual(),
        ]);
    }

    private function nomeDoMesAtual(): string
    {
        $now = Carbon::now();
        $now->locale('pt');

        return $now->translatedFormat('F');
    }

    /**
     * @return Collection<int, Player>
     */
    private function aniversariosDoMes(): Collection
    {
        $month = Carbon::now()->month;

        return Player::query()
            ->with('team')
            ->onCurrentSeasonTeam()
            ->whereNotNull('data_nascimento')
            ->get()
            ->filter(fn (Player $player): bool => $player->data_nascimento?->month === $month)
            ->sortBy(fn (Player $player): string => sprintf('%02d%s', $player->data_nascimento->day, mb_strtolower($player->nome)))
            ->values();
    }
}
