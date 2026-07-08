@props([
    'sponsors',
])

@if ($sponsors->isNotEmpty())
    @php
        $track = collect();

        while ($track->count() < 12) {
            $track = $track->concat($sponsors);
        }
    @endphp

    <section class="mt-16 border-y border-line bg-surface py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-site.section-heading title="Patrocinadores" subtitle="Empresas e entidades que apoiam o nosso clube." />
        </div>

        <div class="sponsors-marquee-track relative mt-2 overflow-hidden">
            <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-surface to-transparent sm:w-24"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-surface to-transparent sm:w-24"></div>

            <div class="sponsors-marquee flex w-max gap-5">
                @foreach (range(1, 2) as $pass)
                    @foreach ($track as $sponsor)
                        <a
                            href="{{ $sponsor->link }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            title="{{ $sponsor->nome }}"
                            @class([
                                'sponsors-marquee-item group flex h-24 w-40 shrink-0 items-center justify-center px-4 sm:w-44',
                                'pointer-events-none' => $pass === 2,
                            ])
                            @if ($pass === 2) aria-hidden="true" tabindex="-1" @endif
                        >
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($sponsor->logo) }}"
                                alt="{{ $sponsor->nome }}"
                                class="max-h-16 max-w-full object-contain opacity-70 grayscale transition duration-300 group-hover:opacity-100 group-hover:grayscale-0"
                                loading="lazy"
                                decoding="async"
                            >
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>
@endif
