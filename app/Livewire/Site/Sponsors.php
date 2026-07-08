<?php

namespace App\Livewire\Site;

use App\Models\Sponsor;
use App\Models\Team;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('Patrocinadores')]
class Sponsors extends Component
{
    public function render(): View
    {
        $equipas = Team::query()
            ->orderBy('ordem')
            ->with(['sponsors'])
            ->get()
            ->filter(fn (Team $team) => $team->sponsors->isNotEmpty());

        return view('livewire.site.sponsors', [
            'equipas' => $equipas,
            'patrocinadoresGerais' => Sponsor::query()->ativos()->gerais()->get(),
        ]);
    }
}
