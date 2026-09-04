<?php

namespace App\View\Components\Site;

use App\Models\Game;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GameCard extends Component
{
    public bool $emCasa;

    public string $topNome;

    public ?int $topPontos;

    public bool $topIsAd;

    public string $bottomNome;

    public ?int $bottomPontos;

    public bool $bottomIsAd;

    public bool $terminado;

    public ?bool $vitoria;

    public function __construct(
        public Game $game,
        public bool $showTeam = true,
    ) {
        $this->emCasa = $game->emCasa();
        $adNome = 'AD Mealhada';
        $advNome = $game->adversario;

        $this->topNome = $this->emCasa ? $adNome : $advNome;
        $this->topPontos = $this->emCasa ? $game->pontos_casa : $game->pontos_fora;
        $this->topIsAd = $this->emCasa;

        $this->bottomNome = $this->emCasa ? $advNome : $adNome;
        $this->bottomPontos = $this->emCasa ? $game->pontos_fora : $game->pontos_casa;
        $this->bottomIsAd = ! $this->emCasa;

        $this->terminado = $game->estado === 'terminado';
        $this->vitoria = $game->vitoria();
    }

    public function render(): View
    {
        return view('components.site.game-card');
    }
}
