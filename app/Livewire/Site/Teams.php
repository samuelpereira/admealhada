<?php

namespace App\Livewire\Site;

use App\Models\Team;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('Equipas')]
class Teams extends Component
{
    public function render()
    {
        return View::make('livewire.site.teams', [
            'equipas' => Team::query()
                ->withCount('jogadores')
                ->orderBy('ordem')
                ->get(),
        ]);
    }
}
