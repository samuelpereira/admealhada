@props([
    'sponsor',
])

<a
    href="{{ $sponsor->link }}"
    target="_blank"
    rel="noopener noreferrer"
    title="{{ $sponsor->nome }}"
    class="site-card group flex h-24 w-full items-center justify-center bg-surface-2 p-3 text-center hover:-translate-y-0.5 sm:h-28"
>
    @if ($sponsor->logo)
        <img
            src="{{ \Illuminate\Support\Facades\Storage::url($sponsor->logo) }}"
            alt="{{ $sponsor->nome }}"
            class="max-h-14 max-w-full object-contain transition group-hover:scale-105 sm:max-h-16"
        >
    @else
        <span class="text-[11px] font-black uppercase leading-tight text-brand-800 transition group-hover:text-rim-600 dark:text-brand-100 dark:group-hover:text-rim-200">
            {{ $sponsor->nome }}
        </span>
    @endif
</a>
