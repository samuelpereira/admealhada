<?php

namespace App\Livewire\Site;

use App\Models\News;
use Illuminate\Support\Facades\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.site')]
class NewsShow extends Component
{
    public News $news;

    public function mount(News $news): void
    {
        abort_if($news->publicado_em === null || $news->publicado_em->isFuture(), 404);

        $this->news = $news;
    }

    public function render()
    {
        return View::make('livewire.site.news-show', [
            'relacionadas' => News::query()
                ->publicadas()
                ->where('id', '!=', $this->news->id)
                ->where('categoria', $this->news->categoria)
                ->take(3)
                ->get(),
        ])->title($this->news->titulo.' - '.config('app.name'));
    }
}
