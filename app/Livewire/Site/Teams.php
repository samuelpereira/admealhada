<?php

namespace App\Livewire\Site;

use App\Models\Season;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.site')]
class Teams extends Component
{
    public Season $season;

    public function mount(?Season $season = null): void
    {
        if ($season?->is_current) {
            abort(301, '', ['Location' => route('equipas')]);
        }

        $this->season = $season ?? Season::query()->current()->firstOrFail();
    }

    public function render(): View
    {
        return view('livewire.site.teams', [
            'equipas' => $this->season->teams()
                ->with(['season'])
                ->withCount('jogadores')
                ->orderBy('ordem')
                ->get(),
            'epocas' => Season::query()->orderByDesc('starts_at')->get(),
        ])->title($this->season->is_current ? 'Equipas' : 'Equipas · '.$this->season->name);
    }
}
