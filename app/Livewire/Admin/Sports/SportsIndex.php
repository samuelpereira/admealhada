<?php

namespace App\Livewire\Admin\Sports;

use App\Models\Sport;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Modalidades')]
class SportsIndex extends Component
{
    public function toggle(Sport $sport): void
    {
        $sport->update(['ativo' => ! $sport->ativo]);

        Flux::toast(text: $sport->ativo ? 'Modalidade ativada.' : 'Modalidade desativada.');
    }

    public function delete(Sport $sport): void
    {
        if ($sport->teams()->exists()) {
            Flux::toast(variant: 'danger', text: 'Não é possível eliminar: existem equipas associadas.');

            return;
        }

        $sport->delete();

        Flux::toast(variant: 'success', text: 'Modalidade eliminada.');
    }

    public function render()
    {
        return view('livewire.admin.sports.index', [
            'sports' => Sport::withCount('teams')->orderBy('nome')->get(),
        ]);
    }
}
