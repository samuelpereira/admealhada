@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'image' => null,
    'seed' => 0,
])

<section @class([
    'relative overflow-hidden text-white',
    'bg-brand-950' => $image,
    'bg-gradient-to-br from-brand-800 via-brand-900 to-brand-950' => ! $image,
])>
    @if ($image)
        <div class="absolute inset-0">
            <x-site.media :src="$image" :seed="$seed" :icon="false" class="h-full w-full" />
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-brand-950/60 via-brand-950/25 to-transparent"></div>
    @else
        <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-brand-500/20 blur-3xl"></div>
    @endif
    <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        @if ($eyebrow)
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-300">{{ $eyebrow }}</span>
        @endif
        <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-3 max-w-2xl text-brand-100/90">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
