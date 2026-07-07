<?php

namespace App\Livewire\Site;

use App\Models\Player;
use App\Models\SocialBody;
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
            'totalEquipas' => Team::count(),
            'totalAtletas' => Player::where('papel', 'jogador')->count(),
            'orgaosSociais' => $orgaosSociais,
            'categorias' => SocialBody::CATEGORIAS,
        ]);
    }
}
