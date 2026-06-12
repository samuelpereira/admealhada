<?php

namespace App\Livewire\Site;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('O Clube')]
class About extends Component
{
    public function render()
    {
        return View::make('livewire.site.about', [
            'totalEquipas' => Team::count(),
            'totalAtletas' => Player::where('papel', 'jogador')->count(),
        ]);
    }
}
