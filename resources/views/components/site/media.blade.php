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
