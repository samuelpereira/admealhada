<?php

namespace App\Livewire\Site;

use App\Models\Player;
use App\Models\SocialBody;
use App\Models\Sponsor;
use App\Models\Team;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('O Clube')]
class About extends Component
{
    public function render(): View
    {
        $orgaosSociais = SocialBody::query()
            ->ativos()
            ->get()
            ->groupBy('categoria');

        return view('livewire.site.about', [
            'totalEquipas' => Team::query()->currentSeason()->count(),
            'totalJogadores' => Player::query()->where('papel', 'jogador')->onCurrentSeasonTeam()->count(),
            'totalPatrocinadores' => Sponsor::where('ativo', true)->count(),
            'orgaosSociais' => $orgaosSociais,
            'categorias' => SocialBody::CATEGORIAS,
        ]);
    }
}
