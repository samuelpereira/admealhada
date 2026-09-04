@props([
    'news',
])

<article class="site-card group flex flex-col hover:-translate-y-1">
    <a href="{{ route('noticias.show', $news) }}" class="block aspect-[16/9] overflow-hidden">
        <x-site.media :src="$news->capa" :label="$news->categoria" :seed="$news->id" class="transition duration-300 group-hover:scale-105" />
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="mb-2 flex flex-wrap items-center gap-2 text-xs">
            <span class="rounded-md bg-rim-100 px-2.5 py-1 font-black uppercase text-rim-800 dark:bg-rim-500/20 dark:text-rim-200">{{ $news->categoria }}</span>
            @if ($news->publicado_em)
                <span class="text-muted">{{ $news->publicado_em->translatedFormat('d M Y') }}</span>
            @endif
        </div>
        <h3 class="text-lg font-black leading-snug text-ink group-hover:text-brand-600 dark:group-hover:text-brand-300">
            <a href="{{ route('noticias.show', $news) }}">{{ $news->titulo }}</a>
        </h3>
        <p class="mt-2 line-clamp-3 flex-1 text-sm text-body">{{ $news->resumo }}</p>
        <a href="{{ route('noticias.show', $news) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-black text-brand-700 transition-all hover:gap-2 hover:text-rim-600 dark:text-brand-200">
            Ler mais
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
        </a>
    </div>
</article>
