<?php

namespace App\Livewire\Admin\Seasons;

use App\Models\Season;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Épocas')]
class SeasonsIndex extends Component
{
    public function markCurrent(int $seasonId): void
    {
        $season = Season::query()->findOrFail($seasonId);
        $season->markAsCurrent();

        Flux::toast(variant: 'success', text: $season->name.' marcada como época actual.');
    }

    public function delete(int $seasonId): void
    {
        $season = Season::query()->findOrFail($seasonId);

        if ($season->is_current) {
            Flux::toast(variant: 'danger', text: 'Não é possível eliminar a época actual.');

            return;
        }

        if ($season->teams()->exists()) {
            Flux::toast(variant: 'danger', text: 'Não é possível eliminar: existem equipas associadas.');

            return;
        }

        $season->delete();

        Flux::toast(variant: 'success', text: 'Época eliminada.');
    }

    public function render(): View
    {
        return view('livewire.admin.seasons.index', [
            'epocas' => Season::query()
                ->withCount('teams')
                ->orderByDesc('starts_at')
                ->get(),
        ]);
    }
}
