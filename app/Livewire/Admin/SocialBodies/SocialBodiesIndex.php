<?php

namespace App\Livewire\Admin\SocialBodies;

use App\Models\SocialBody;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Órgãos sociais')]
class SocialBodiesIndex extends Component
{
    public function toggle(SocialBody $socialBody): void
    {
        $socialBody->update(['ativo' => ! $socialBody->ativo]);

        Flux::toast(text: $socialBody->ativo ? 'Membro ativado.' : 'Membro desativado.');
    }

    public function delete(SocialBody $socialBody): void
    {
        $socialBody->delete();

        Flux::toast(variant: 'success', text: 'Membro eliminado.');
    }

    public function render(): View
    {
        return view('livewire.admin.social-bodies.index', [
            'socialBodies' => SocialBody::query()
                ->orderBy('categoria')
                ->orderBy('ordem')
                ->get()
                ->sortBy(fn (SocialBody $membro): string => $membro->categoria.sprintf('%010d', $membro->ordem).mb_strtolower($membro->nome))
                ->values(),
            'categorias' => SocialBody::CATEGORIAS,
        ]);
    }
}
