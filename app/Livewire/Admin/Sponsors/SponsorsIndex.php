<?php

namespace App\Livewire\Admin\Sponsors;

use App\Models\Sponsor;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Patrocinadores')]
class SponsorsIndex extends Component
{
    public function toggle(Sponsor $sponsor): void
    {
        $sponsor->update(['ativo' => ! $sponsor->ativo]);

        Flux::toast(text: $sponsor->ativo ? 'Patrocinador ativado.' : 'Patrocinador desativado.');
    }

    public function delete(Sponsor $sponsor): void
    {
        if ($sponsor->logo && Storage::disk('public')->exists($sponsor->logo)) {
            Storage::disk('public')->delete($sponsor->logo);
        }

        $sponsor->delete();

        Flux::toast(variant: 'success', text: 'Patrocinador eliminado.');
    }

    public function render(): View
    {
        return view('livewire.admin.sponsors.index', [
            'sponsors' => Sponsor::query()
                ->with('teams')
                ->orderBy('tipo')
                ->orderBy('ordem')
                ->orderBy('nome')
                ->get(),
        ]);
    }
}
