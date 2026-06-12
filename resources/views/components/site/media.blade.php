@props([
    'src' => null,
    'label' => null,
    'seed' => 0,
    'icon' => true,
])

@php
    $gradients = [
        'from-brand-500 to-brand-800',
        'from-brand-700 to-brand-950',
        'from-violet-500 to-brand-800',
        'from-fuchsia-600 to-brand-900',
        'from-brand-600 to-indigo-800',
        'from-purple-600 to-brand-950',
    ];
    $idx = is_numeric($seed) ? (int) $seed : crc32((string) $seed);
    $gradient = $gradients[$idx % count($gradients)];

    $resolvedSrc = $src
        ? (\Illuminate\Support\Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : \Illuminate\Support\Facades\Storage::url($src))
        : null;
@endphp

@if ($resolvedSrc)
    <img src="{{ $resolvedSrc }}" alt="{{ $label }}" {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}>
@else
    <div {{ $attributes->merge(['class' => "relative flex h-full w-full items-center justify-center bg-gradient-to-br {$gradient} overflow-hidden"]) }}>
        <div class="pointer-events-none absolute -right-6 -top-6 h-28 w-28 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-black/10"></div>
        @if ($icon)
            <svg class="h-12 w-12 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="9" />
                <path d="M3 12h18M12 3v18M5.6 5.6c3 2.5 3 10.3 0 12.8M18.4 5.6c-3 2.5-3 10.3 0 12.8" />
            </svg>
        @endif
        @if ($label)
            <span class="absolute bottom-2 left-3 right-3 truncate text-xs font-semibold text-white/80">{{ $label }}</span>
        @endif
    </div>
@endif
