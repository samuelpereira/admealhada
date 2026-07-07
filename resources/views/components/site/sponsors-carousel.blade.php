@props([
    'sponsors',
])

@if ($sponsors->isNotEmpty())
    @php
        $track = collect();

        while ($track->count() < 16) {
            $track = $track->concat($sponsors);
        }
    @endphp

    <section class="border-y border-line bg-surface py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-site.section-heading title="Patrocinadores" subtitle="Empresas e entidades que apoiam o nosso clube." />
        </div>

        <div class="sponsors-marquee-track relative mt-2 overflow-hidden">
            <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-surface to-transparent sm:w-24"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-surface to-transparent sm:w-24"></div>

            <div class="flex w-max animate-sponsors-marquee py-4">
                @foreach ([false, true] as $isClone)
                    <div @class([
                        'flex shrink-0 items-center gap-12 sm:gap-16',
                        'pointer-events-none' => $isClone,
                    ]) @if ($isClone) aria-hidden="true" @endif>
                        @foreach ($track as $sponsor)
                            <a
                                href="{{ $sponsor->link }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="{{ $sponsor->nome }}"
                                @class([
                                    'flex h-20 w-36 shrink-0 items-center justify-center px-2 transition hover:scale-105 sm:w-44',
                                    'pointer-events-none' => $isClone,
                                ])
                            >
                                <img
                                    src="{{ \Illuminate\Support\Facades\Storage::url($sponsor->logo) }}"
                                    alt="{{ $sponsor->nome }}"
                                    class="max-h-16 max-w-full object-contain opacity-75 grayscale transition hover:opacity-100 hover:grayscale-0"
                                >
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
