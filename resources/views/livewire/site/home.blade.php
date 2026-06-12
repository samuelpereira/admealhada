<div>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-900 to-brand-950 text-white">
        <div class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -left-24 h-96 w-96 rounded-full bg-fuchsia-500/10 blur-3xl"></div>
        <div class="relative mx-auto flex max-w-7xl flex-col items-center gap-10 px-4 py-16 sm:px-6 lg:flex-row lg:py-24 lg:px-8">
            <div class="flex-1 text-center lg:text-left">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-100 ring-1 ring-white/20">
                    Basquetebol · Mealhada
                </span>
                <h1 class="mt-5 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    AD Mealhada
                </h1>
                <p class="mx-auto mt-5 max-w-xl text-lg text-brand-100/90 lg:mx-0">
                    Paixão pelo basquetebol, do minibasquete aos seniores na CN2. Formamos atletas e pessoas, dentro e fora do campo.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                    <a href="{{ route('equipas') }}" class="rounded-lg bg-white px-6 py-3 text-sm font-bold text-brand-800 shadow-lg transition hover:bg-brand-50">
                        Conhecer as equipas
                    </a>
                    <a href="{{ route('calendario') }}" class="rounded-lg border border-white/30 bg-white/5 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10">
                        Calendário de jogos
                    </a>
                </div>
            </div>
            <div class="flex flex-1 justify-center">
                <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="AD Mealhada" class="h-56 w-56 object-contain drop-shadow-2xl sm:h-72 sm:w-72 lg:h-80 lg:w-80">
            </div>
        </div>
    </section>

    {{-- Jogos --}}
    <section class="mx-auto max-w-7xl space-y-12 px-4 py-14 sm:px-6 lg:px-8">
        <div>
            <x-site.section-heading title="Próximos jogos" :link="route('calendario')" link-label="Ver calendário" />
            @if ($proximosJogos->isEmpty())
                <p class="rounded-xl border border-dashed border-line bg-surface p-6 text-sm text-muted">Sem jogos agendados de momento.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($proximosJogos as $game)
                        <x-site.game-card :game="$game" />
                    @endforeach
                </div>
            @endif
        </div>
        <div>
            <x-site.section-heading title="Últimos resultados" :link="route('calendario')" link-label="Ver todos" />
            @if ($ultimosResultados->isEmpty())
                <p class="rounded-xl border border-dashed border-line bg-surface p-6 text-sm text-muted">Ainda não há resultados registados.</p>
            @else
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($ultimosResultados as $game)
                        <x-site.game-card :game="$game" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Notícias --}}
    <section class="bg-surface py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-site.section-heading title="Notícias e novidades" subtitle="Fica a par de tudo o que se passa no clube." :link="route('noticias')" />

            <div class="grid gap-6 lg:grid-cols-3">
                @if ($destaque)
                    <a href="{{ route('noticias.show', $destaque) }}" class="group relative col-span-1 flex min-h-72 flex-col justify-end overflow-hidden rounded-2xl lg:col-span-2 lg:row-span-2">
                        <x-site.media :src="$destaque->capa" :seed="$destaque->id" :icon="false" class="absolute inset-0" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                        <div class="relative p-6 lg:p-8">
                            <span class="rounded-full bg-brand-600 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">{{ $destaque->categoria }}</span>
                            <h3 class="mt-3 text-2xl font-extrabold text-white lg:text-3xl">{{ $destaque->titulo }}</h3>
                            <p class="mt-2 line-clamp-2 max-w-2xl text-sm text-white/80">{{ $destaque->resumo }}</p>
                        </div>
                    </a>
                @endif

                <div class="grid gap-6">
                    @foreach ($noticias->where('id', '!=', optional($destaque)->id)->take(2) as $news)
                        <x-site.news-card :news="$news" />
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Escalões --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <x-site.section-heading title="As nossas equipas" subtitle="Sete escalões, uma só família." :link="route('equipas')" />
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($equipas as $team)
                <a href="{{ route('equipas.show', $team) }}" class="group relative flex aspect-square flex-col justify-end overflow-hidden rounded-2xl shadow-sm transition hover:shadow-lg">
                    <x-site.media :src="$team->foto" :seed="$team->id" :icon="false" class="absolute inset-0 transition duration-300 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/30 to-transparent"></div>
                    <div class="relative p-4">
                        <span class="text-xs font-semibold uppercase tracking-wide text-brand-200">{{ $team->escalao }}</span>
                        <h3 class="text-base font-extrabold leading-tight text-white">{{ $team->nome }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Modalidades --}}
    @if ($modalidades->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <x-site.section-heading title="Modalidades" subtitle="As modalidades desportivas da AD Mealhada." />
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($modalidades as $modalidade)
                    <div class="rounded-2xl border border-brand-200 bg-brand-50 p-6 dark:border-brand-500/30 dark:bg-brand-500/10">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3v18M5.6 5.6c3 2.5 3 10.3 0 12.8M18.4 5.6c-3 2.5-3 10.3 0 12.8" /></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-extrabold text-ink">{{ $modalidade->nome }}</h3>
                                @if ($modalidade->descricao)
                                    <p class="mt-1 text-sm text-body">{{ $modalidade->descricao }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Patrocinadores --}}
    <x-site.sponsors-carousel :sponsors="$patrocinadores" />

    {{-- CTA --}}
    <section class="bg-gradient-to-r from-brand-700 to-brand-900">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-4 py-12 text-center sm:px-6 lg:flex-row lg:px-8 lg:text-left">
            <div>
                <h2 class="text-2xl font-extrabold text-white sm:text-3xl">Queres jogar pela AD Mealhada?</h2>
                <p class="mt-2 text-brand-100/90">Inscrições abertas para todos os escalões de formação. Vem treinar connosco!</p>
            </div>
            <a href="{{ route('contactos') }}" class="shrink-0 rounded-lg bg-white px-6 py-3 text-sm font-bold text-brand-800 shadow-lg transition hover:bg-brand-50">
                Contacta-nos
            </a>
        </div>
    </section>
</div>
