<?php

namespace App\Livewire\Admin\Teams;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Team;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Equipas')]
class TeamsIndex extends Component
{
    use HandlesImageUpload;

    public function delete(Team $team): void
    {
        $this->deleteImage($team->foto);
        $team->delete();

        Flux::toast(variant: 'success', text: 'Equipa eliminada.');
    }

    public function render(): View
    {
        return view('livewire.admin.teams.index', [
            'teams' => Team::query()
                ->with('sport')
                ->withCount('players')
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get(),
        ]);
    }
}
