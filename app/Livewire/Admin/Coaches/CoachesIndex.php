<?php

namespace App\Livewire\Admin\Coaches;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Coach;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Treinadores')]
class CoachesIndex extends Component
{
    use HandlesImageUpload;

    public function delete(Coach $coach): void
    {
        $this->deleteImage($coach->foto);

        $coach->delete();

        Flux::toast(variant: 'success', text: 'Treinador eliminado.');
    }

    public function render(): View
    {
        return view('livewire.admin.coaches.coaches-index', [
            'coaches' => Coach::query()
                ->with('teams.season')
                ->orderBy('nome')
                ->get(),
        ]);
    }
}
