<?php

namespace App\Livewire\Site;

use App\Models\Album;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.site')]
class AlbumShow extends Component
{
    public Album $album;

    public function mount(Album $album): void
    {
        $this->album = $album->load(['photos', 'team']);
    }

    public function render(): View
    {
        return ViewFacade::make('livewire.site.album-show')
            ->title($this->album->titulo.' - '.config('app.name'));
    }
}
