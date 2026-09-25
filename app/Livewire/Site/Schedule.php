<?php

namespace App\Livewire\Site;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('Calendário')]
class Schedule extends Component
{
    #[Url(as: 'equipa')]
    public string $teamId = '';

    #[Url(as: 'epoca')]
    public string $epoca = '';

    #[Url(as: 'competicao')]
    public string $competicaoFilter = '';

    public int $resultadosLimit = 20;

    public function mount(): void
    {
        if ($this->epoca === '') {
            $this->epoca = Game::epocaAtual();
        }

        $this->forgetTeamOutsideSeason();
    }

    public function loadMoreResultados(): void
    {
        $this->resultadosLimit += 20;
    }

    public function updatedTeamId(): void
    {
        $this->resetResultadosLimit();

        $this->forgetCompeticaoOutsideOptions();
    }

    public function updatedEpoca(): void
    {
        $this->resetResultadosLimit();
        $this->forgetTeamOutsideSeason();
        $this->forgetCompeticaoOutsideOptions();
    }

    public function updatedCompeticaoFilter(): void
    {
        $this->resetResultadosLimit();
    }

    protected function resetResultadosLimit(): void
    {
        $this->resultadosLimit = 20;
    }

    protected function forgetTeamOutsideSeason(): void
    {
        if ($this->teamId === '') {
            return;
        }

        $belongsToSeason = $this->equipaOptions()->contains(
            fn (Team $team): bool => (string) $team->id === $this->teamId,
        );

        if (! $belongsToSeason) {
            $this->teamId = '';
        }
    }

    protected function forgetCompeticaoOutsideOptions(): void
    {
        if ($this->competicaoFilter !== '' && ! array_key_exists($this->competicaoFilter, $this->competicaoOptions())) {
            $this->competicaoFilter = '';
        }
    }

    /**
     * @return Collection<int, Team>
     */
    protected function equipaOptions(): Collection
    {
        return Team::query()
            ->whereRelation('season', 'name', $this->epoca)
            ->orderBy('ordem')
            ->orderBy('nome')
            ->get();
    }

    /**
     * @return list<string>
     */
    protected function epocaOptions(): array
    {
        $predefinidas = ['2025/2026', '2026/2027'];

        $dasBd = Game::query()
            ->when($this->teamId !== '', fn ($q) => $q->where('team_id', $this->teamId))
            ->whereNotNull('data_hora')
            ->pluck('data_hora')
            ->map(fn ($data) => Game::epocaFromDate(Carbon::parse($data)))
            ->unique();

        /** @var list<string> $epocas */
        $epocas = collect($predefinidas)
            ->merge($dasBd)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $epocas;
    }

    /**
     * @return array<string, string>
     */
    protected function competicaoOptions(): array
    {
        $competicoes = Game::query()
            ->when($this->teamId !== '', fn ($q) => $q->where('team_id', $this->teamId))
            ->epoca($this->epoca)
            ->whereNotNull('competicao')
            ->where('competicao', '!=', '')
            ->distinct()
            ->pluck('competicao');

        $options = [];

        foreach ($competicoes as $competicao) {
            $key = Game::competicaoFilterKey($competicao);

            if ($key && ! isset($options[$key])) {
                $options[$key] = Game::competicaoLabelFrom($competicao);
            }
        }

        asort($options);

        return $options;
    }

    public function render(): View
    {
        $proximos = Game::query()
            ->with('team.season')
            ->proximos()
            ->epoca($this->epoca)
            ->when($this->teamId !== '', fn ($q) => $q->where('team_id', $this->teamId))
            ->competicaoFilter($this->competicaoFilter)
            ->get();

        $resultadosQuery = Game::query()
            ->with('team.season')
            ->resultados()
            ->epoca($this->epoca)
            ->when($this->teamId !== '', fn ($q) => $q->where('team_id', $this->teamId))
            ->competicaoFilter($this->competicaoFilter);

        $totalResultados = (clone $resultadosQuery)->count();

        $resultados = (clone $resultadosQuery)
            ->take($this->resultadosLimit)
            ->get();

        return view('livewire.site.schedule', [
            'equipas' => $this->equipaOptions(),
            'epocas' => $this->epocaOptions(),
            'competicoes' => $this->competicaoOptions(),
            'proximos' => $proximos,
            'resultados' => $resultados,
            'hasMoreResultados' => $totalResultados > $resultados->count(),
        ]);
    }
}
