<div>
    @push('structured-data')
        @php
            $newsImage = filled($news->capa)
                ? url(\Illuminate\Support\Facades\Storage::url($news->capa))
                : Vite::asset('resources/images/hero-hand-ball.png');

            $newsSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => $news->titulo,
                'description' => $news->resumo ?: \Illuminate\Support\Str::limit(trim(strip_tags((string) $news->corpo)), 155),
                'image' => [$newsImage],
                'datePublished' => $news->publicado_em?->toIso8601String(),
                'dateModified' => $news->updated_at?->toIso8601String(),
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
        @endphp

        <script type="application/ld+json">@json($newsSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    @endpush

    <article>
        {{-- Cabeçalho com capa --}}
        <header class="court-surface relative overflow-hidden bg-brand-950 text-white">
            <div class="absolute inset-0 opacity-40">
                <x-site.media :src="$news->capa" :seed="$news->id" :icon="false" class="h-full w-full" />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-950/70 to-brand-950/40"></div>
            <div class="relative mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <a href="{{ route('noticias') }}" class="inline-flex min-h-10 items-center gap-1 text-sm font-black text-brand-200 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    Todas as notícias
                </a>
                <div class="mt-5 flex items-center gap-3 text-sm">
                    <span class="rounded-md bg-rim-500 px-3 py-1 text-xs font-black uppercase">{{ $news->categoria }}</span>
                    @if ($news->publicado_em)
                        <span class="text-brand-200">{{ $news->publicado_em->translatedFormat('d \d\e F \d\e Y') }}</span>
                    @endif
                </div>
                <h1 class="mt-4 text-3xl font-black uppercase leading-tight sm:text-4xl">{{ $news->titulo }}</h1>
                @if ($news->resumo)
                    <p class="mt-4 text-lg text-brand-100/90">{{ $news->resumo }}</p>
                @endif
            </div>
        </header>

        {{-- Corpo --}}
        <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="article">
                {!! $news->corpo !!}
            </div>
        </div>
    </article>

    @if ($relacionadas->isNotEmpty())
        <section class="border-t border-line bg-surface/80 py-12">
            <div class="site-shell">
                <x-site.section-heading title="Notícias relacionadas" />
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relacionadas as $news)
                        <x-site.news-card :news="$news" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
