@props([
    'title',
    'subtitle' => null,
    'link' => null,
    'linkLabel' => 'Ver todos',
])

<div class="mb-7 flex items-end justify-between gap-4 sm:mb-8">
    <div class="min-w-0">
        <div class="flex items-center gap-3">
            <span class="h-1.5 w-8 rounded-full bg-rim-500"></span>
            <h2 class="text-2xl font-black uppercase text-current sm:text-3xl">{{ $title }}</h2>
        </div>
        @if ($subtitle)
            <p class="mt-2 max-w-2xl text-sm leading-6 opacity-70">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($link)
        <a href="{{ $link }}" class="hidden shrink-0 items-center gap-1 rounded-md bg-surface px-3 py-2 text-sm font-black text-brand-700 ring-1 ring-line transition-all hover:gap-2 hover:text-rim-600 dark:text-brand-200 sm:inline-flex">
            {{ $linkLabel }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
        </a>
    @endif
</div>
