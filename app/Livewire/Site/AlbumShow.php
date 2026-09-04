<?php

namespace App\Livewire\Site;

use App\Models\Album;
use App\Models\Photo;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.site')]
class AlbumShow extends Component
{
    /**
     * @var list<string>
     */
    private const PHOTO_GRADIENTS = [
        'from-brand-500 to-brand-800',
        'from-brand-700 to-brand-950',
        'from-violet-500 to-brand-800',
        'from-fuchsia-600 to-brand-900',
        'from-brand-600 to-indigo-800',
        'from-purple-600 to-brand-950',
    ];

    public Album $album;

    public function mount(Album $album): void
    {
        $this->album = $album->load(['photos', 'team']);
    }

    public function render(): View
    {
        return ViewFacade::make('livewire.site.album-show', [
            'photosData' => $this->photosData(),
        ])->title($this->album->titulo.' - '.config('app.name'));
    }

    /**
     * @return array<int, array{src: string|null, legenda: string|null, gradient: string}>
     */
    protected function photosData(): array
    {
        $gradients = self::PHOTO_GRADIENTS;

        return $this->album->photos
            ->map(fn (Photo $photo): array => [
                'src' => MediaUrl::resolve($photo->caminho),
                'legenda' => $photo->legenda,
                'gradient' => $gradients[$photo->id % count($gradients)],
            ])
            ->values()
            ->all();
    }
}
