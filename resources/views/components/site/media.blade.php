@props([
    'src' => null,
    'label' => null,
    'seed' => 0,
    'icon' => true,
])

@php
    $gradients = [
        'from-brand-700 via-brand-900 to-rim-700',
        'from-brand-950 via-brand-700 to-rim-500',
        'from-zinc-900 via-brand-800 to-rim-600',
        'from-rim-600 via-brand-700 to-brand-950',
        'from-brand-800 via-zinc-900 to-rim-700',
        'from-brand-600 via-rim-600 to-brand-950',
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
    <div {{ $attributes->merge(['class' => "court-lines relative flex h-full w-full items-center justify-center overflow-hidden bg-gradient-to-br {$gradient}"]) }}>
        <div class="absolute inset-0 bg-black/10"></div>
        @if ($icon)
            <svg class="relative h-12 w-12 text-white/55" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="9" />
                <path d="M3 12h18M12 3v18M5.6 5.6c3 2.5 3 10.3 0 12.8M18.4 5.6c-3 2.5-3 10.3 0 12.8" />
            </svg>
        @endif
        @if ($label)
            <span class="absolute bottom-2 left-3 right-3 truncate text-xs font-semibold text-white/80">{{ $label }}</span>
        @endif
    </div>
@endif
