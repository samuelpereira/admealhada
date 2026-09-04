<?php

namespace App\Livewire\Site;

use App\Models\News;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Illuminate\View\View;
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

    public function render(): View
    {
        return ViewFacade::make('livewire.site.news-show', [
            'relacionadas' => News::query()
                ->publicadas()
                ->where('id', '!=', $this->news->id)
                ->where('categoria', $this->news->categoria)
                ->take(3)
                ->get(),
            'newsSchema' => $this->newsSchema(),
        ])->title($this->news->titulo.' - '.config('app.name'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function newsSchema(): array
    {
        $image = MediaUrl::absolute($this->news->capa, Vite::asset('resources/images/hero-hand-ball.png'));

        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $this->news->titulo,
            'description' => $this->news->resumo ?: Str::limit(trim(strip_tags((string) $this->news->corpo)), 155),
            'image' => [$image],
            'datePublished' => $this->news->publicado_em?->toIso8601String(),
            'dateModified' => $this->news->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => config('site.contact.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('site.contact.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => Vite::asset('resources/images/logo.png'),
                ],
            ],
            'mainEntityOfPage' => url()->current(),
        ];
    }
}
