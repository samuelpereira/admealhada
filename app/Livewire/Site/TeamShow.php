<?php

namespace App\Livewire\Site;

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

    public function mount(Team $team): void
    {
        $this->team = $team->load(['sport', 'players', 'sponsors']);
    }

    public function render(): View
    {
        return ViewFacade::make('livewire.site.team-show', [
            'jogadores' => $this->team->players->where('papel', 'jogador'),
            'staff' => $this->team->players->where('papel', '!=', 'jogador'),
            'proximosJogos' => $this->team->games()->proximos()->take(5)->get(),
            'resultados' => $this->team->games()->resultados()->take(8)->get(),
            'teamSchema' => $this->teamSchema(),
        ])->title($this->team->nome.' - '.config('app.name'));
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
            'url' => url()->current(),
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
