@props([
    'title',
    'subtitle' => null,
    'link' => null,
    'linkLabel' => 'Ver todos',
])

<div class="mb-8 flex items-end justify-between gap-4">
    <div>
        <div class="flex items-center gap-2">
            <span class="h-6 w-1.5 rounded-full bg-brand-600"></span>
            <h2 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">{{ $title }}</h2>
        </div>
        @if ($subtitle)
            <p class="mt-2 text-sm text-muted">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($link)
        <a href="{{ $link }}" class="hidden shrink-0 items-center gap-1 text-sm font-semibold text-brand-600 transition-all hover:gap-2 dark:text-brand-300 sm:inline-flex">
            {{ $linkLabel }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
        </a>
    @endif
</div>
