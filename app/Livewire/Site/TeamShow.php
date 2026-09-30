<?php

namespace App\Livewire\Site;

use App\Models\Season;
use App\Models\Team;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.site')]
class TeamShow extends Component
{
    public Team $team;

    public function mount(Team $team, ?Season $season = null): void
    {
        if ($season?->is_current) {
            abort(301, '', ['Location' => route('equipas.show', $team)]);
        }

        $this->team = $team->load(['sport', 'players', 'teamSponsors', 'coaches', 'season']);
    }

    public function render(): View
    {
        $title = $this->team->nome.' - '.config('app.name');

        if ($this->team->season && ! $this->team->season->is_current) {
            $title = $this->team->nome.' · '.$this->team->season->name.' - '.config('app.name');
        }

        return ViewFacade::make('livewire.site.team-show', [
            'jogadores' => $this->team->players->where('papel', 'jogador')->where('ativo', true),
            'staff' => $this->team->players->where('papel', '!=', 'jogador')->where('ativo', true),
            'proximosJogos' => $this->team->games()->with('team.season')->proximos()->take(5)->get(),
            'resultados' => $this->team->games()->with('team.season')->resultados()->take(8)->get(),
            'teamSchema' => $this->teamSchema(),
        ])->title($title);
    }

    /**
     * @return array<string, mixed>
     */
    protected function teamSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'SportsTeam',
            'name' => $this->team->nome,
            'sport' => 'Basketball',
            'url' => $this->team->publicShowUrl(),
            'image' => MediaUrl::absolute($this->team->foto, Vite::asset('resources/images/hero-hand-ball.png')),
            'description' => $this->team->descricao ?: 'Equipa de basquetebol da AD Mealhada.',
            'memberOf' => [
                '@type' => 'SportsOrganization',
                'name' => config('site.contact.name'),
                'url' => url('/'),
            ],
        ];
    }
}
