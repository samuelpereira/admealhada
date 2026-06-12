<div>
    <article>
        {{-- Cabeçalho com capa --}}
        <header class="relative overflow-hidden bg-brand-950 text-white">
            <div class="absolute inset-0 opacity-40">
                <x-site.media :src="$news->capa" :seed="$news->id" :icon="false" class="h-full w-full" />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-brand-950 via-brand-950/70 to-brand-950/40"></div>
            <div class="relative mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <a href="{{ route('noticias') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-200 hover:text-white">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    Todas as notícias
                </a>
                <div class="mt-5 flex items-center gap-3 text-sm">
                    <span class="rounded-full bg-brand-600 px-3 py-1 text-xs font-bold uppercase tracking-wide">{{ $news->categoria }}</span>
                    @if ($news->publicado_em)
                        <span class="text-brand-200">{{ $news->publicado_em->translatedFormat('d \d\e F \d\e Y') }}</span>
                    @endif
                </div>
                <h1 class="mt-4 text-3xl font-black leading-tight sm:text-4xl">{{ $news->titulo }}</h1>
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
        <section class="border-t border-line bg-surface py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
