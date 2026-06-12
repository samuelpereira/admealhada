<?php

namespace App\Livewire\Site;

use App\Models\Album;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('Galeria')]
class Gallery extends Component
{
    public function render(): View
    {
        return view('livewire.site.gallery', [
            'albuns' => Album::query()
                ->with('team')
                ->withCount('photos')
                ->orderByDesc('data')
                ->get(),
        ]);
    }
}
