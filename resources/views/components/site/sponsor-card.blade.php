@props([
    'sponsor',
])

<a
    href="{{ $sponsor->link }}"
    target="_blank"
    rel="noopener noreferrer"
    title="{{ $sponsor->nome }}"
    class="group flex h-24 w-full items-center justify-center rounded-lg border border-line bg-surface-2 p-3 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-sm sm:h-28"
>
    <img
        src="{{ \Illuminate\Support\Facades\Storage::url($sponsor->logo) }}"
        alt="{{ $sponsor->nome }}"
        class="max-h-14 max-w-full object-contain transition group-hover:scale-105 sm:max-h-16"
    >
</a>
