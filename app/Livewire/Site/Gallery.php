<?php

namespace App\Livewire\Site;

use App\Models\Album;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.site')]
#[Title('Galeria')]
class Gallery extends Component
{
    public function render()
    {
        return View::make('livewire.site.gallery', [
            'albuns' => Album::query()
                ->with('team')
                ->withCount('photos')
                ->orderByDesc('data')
                ->get(),
        ]);
    }
}
