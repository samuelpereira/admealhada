<?php

namespace App\Livewire\Admin\Albums;

use App\Livewire\Concerns\HandlesImageUpload;
use App\Models\Album;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Galerias')]
class AlbumsIndex extends Component
{
    use HandlesImageUpload;

    public function delete(Album $album): void
    {
        foreach ($album->photos as $photo) {
            $this->deleteImage($photo->caminho);
        }

        $this->deleteImage($album->capa);
        $album->delete();

        Flux::toast(variant: 'success', text: 'Galeria eliminada.');
    }

    public function render()
    {
        return view('livewire.admin.albums.index', [
            'albums' => Album::query()
                ->with('team')
                ->withCount('photos')
                ->orderByDesc('data')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }
}
