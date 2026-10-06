<?php

namespace App\Livewire\Admin;

use App\Models\Album;
use App\Models\Coach;
use App\Models\Game;
use App\Models\News;
use App\Models\Player;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
     * One row per person in the current season. The same player fpb_id on several teams counts once.
     *
     * @return Collection<int, array{nome: string, dia: int, equipas_nomes: string, idade: int, aniversario_hoje: bool}>
     */
    private function aniversariosDoMes(): Collection
    {
        $month = Carbon::now()->month;

        $jogadores = Player::query()
            ->with('team')
            ->onCurrentSeasonTeam()
            ->whereNotNull('data_nascimento')
            ->get()
            ->filter(fn (Player $player): bool => $player->data_nascimento?->month === $month)
            ->groupBy(fn (Player $player): string => $player->fpb_id ?? 'id:'.$player->id)
            ->map(function (EloquentCollection $rows): array {
                /** @var Player $player */
                $player = $rows->first();
                /** @var Carbon $birthDate */
                $birthDate = $player->data_nascimento;

                return $this->cartaoAniversario(
                    $player->nome,
                    $birthDate,
                    $rows
                        ->map(fn (Player $row): ?string => $row->team?->nome)
                        ->filter()
                        ->unique()
                        ->sort()
                        ->implode(', '),
                );
            });

        $seasonId = Season::current()?->id;

        $treinadores = Coach::query()
            ->whereNotNull('data_nascimento')
            ->whereHas('teams', fn (Builder $query) => $query->where('season_id', $seasonId))
            ->get()
            ->filter(fn (Coach $coach): bool => $coach->data_nascimento?->month === $month)
            ->map(function (Coach $coach): array {
                /** @var Carbon $birthDate */
                $birthDate = $coach->data_nascimento;

                return $this->cartaoAniversario($coach->nome, $birthDate, 'Treinador');
            });

        return $jogadores
            ->concat($treinadores)
            ->sortBy(fn (array $pessoa): string => sprintf('%02d%s', $pessoa['dia'], mb_strtolower($pessoa['nome'])))
            ->values();
    }

    /**
     * @return array{nome: string, dia: int, equipas_nomes: string, idade: int, aniversario_hoje: bool}
     */
    private function cartaoAniversario(string $nome, Carbon $birthDate, string $equipas): array
    {
        $today = Carbon::now();

        return [
            'nome' => $nome,
            'dia' => $birthDate->day,
            'equipas_nomes' => $equipas,
            'idade' => $today->year - $birthDate->year,
            'aniversario_hoje' => $birthDate->month === $today->month && $birthDate->day === $today->day,
        ];
    }
}
