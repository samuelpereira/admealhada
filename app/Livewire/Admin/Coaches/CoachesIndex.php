<?php

namespace App\Livewire\Admin\Coaches;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Coach;
use App\Support\NameFilter;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Treinadores')]
class CoachesIndex extends Component
{
    use HandlesImageUpload;

    #[Url(except: '')]
    public string $pesquisa = '';

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
                ->get()
                ->filter(fn (Coach $coach): bool => NameFilter::matches($coach->nome, $this->pesquisa))
                ->sortBy(fn (Coach $coach): string => mb_strtolower($coach->nome))
                ->values(),
        ]);
    }
}
