@props([
    'sponsors',
])

@if ($sponsors->isNotEmpty())
    <section class="border-y border-line bg-surface py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-site.section-heading title="Patrocinadores" subtitle="Empresas e entidades que apoiam o nosso clube." />
        </div>

        <div class="relative mt-2 overflow-hidden">
            <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-surface to-transparent sm:w-24"></div>
            <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-surface to-transparent sm:w-24"></div>

            <div class="flex w-max animate-sponsors-marquee items-center gap-12 py-4 sm:gap-16">
                @foreach ([1, 2] as $pass)
                    @foreach ($sponsors as $sponsor)
                        <a
                            href="{{ $sponsor->link }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            title="{{ $sponsor->nome }}"
                            class="flex h-20 w-36 shrink-0 items-center justify-center px-2 transition hover:scale-105 sm:w-44"
                        >
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($sponsor->logo) }}"
                                alt="{{ $sponsor->nome }}"
                                class="max-h-16 max-w-full object-contain opacity-75 grayscale transition hover:opacity-100 hover:grayscale-0"
                            >
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>
@endif
