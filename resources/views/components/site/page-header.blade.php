@props([
    'title',
    'subtitle' => null,
    'eyebrow' => null,
    'image' => null,
    'seed' => 0,
])

<section @class([
    'court-surface relative overflow-hidden text-white',
    'bg-brand-950' => $image,
    'court-lines' => ! $image,
])>
    @if ($image)
        <div class="absolute inset-0">
            <x-site.media :src="$image" :seed="$seed" :icon="false" class="h-full w-full" />
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-brand-950/80 via-brand-950/35 to-brand-950/25"></div>
    @else
        <div class="absolute inset-x-0 bottom-0 h-1 bg-rim-500"></div>
    @endif
    <div class="site-shell relative py-12 sm:py-14 lg:py-16">
        @if ($eyebrow)
            <span class="inline-flex rounded-md bg-white/10 px-3 py-1 text-xs font-black uppercase text-rim-200 ring-1 ring-white/15">{{ $eyebrow }}</span>
        @endif
        <h1 class="mt-3 max-w-4xl text-3xl font-black uppercase leading-tight sm:text-4xl lg:text-5xl">{{ $title }}</h1>
        @if ($subtitle)
            <p class="mt-3 max-w-2xl text-base leading-7 text-brand-100/90 sm:text-lg">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
