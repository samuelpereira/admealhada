<div>
    {{-- Hero --}}
    <section class="power-hero relative min-h-[calc(100svh-4rem)] overflow-hidden text-white lg:min-h-[720px]">
        <img
            src="{{ Vite::asset('resources/images/hero-hand-ball.png') }}"
            alt="Mão a segurar uma bola de basquetebol"
            class="hero-image-mask absolute inset-y-0 right-0 h-full w-full object-cover object-[82%_center] opacity-95 sm:object-[86%_center] lg:w-[74%]"
        >
        <div class="absolute inset-0 bg-gradient-to-r from-black via-brand-950/90 to-brand-950/20"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_78%_42%,transparent_0_18rem,rgba(0,0,0,0.18)_30rem,rgba(0,0,0,0.54)_100%)]"></div>
        <div class="absolute inset-x-0 bottom-0 h-px bg-white/15"></div>

        <div class="site-shell relative flex min-h-[calc(100svh-4rem)] items-center py-12 sm:py-16 lg:min-h-[720px] lg:py-20">
            <div class="max-w-3xl text-center lg:text-left">
                <span class="inline-flex items-center gap-2 rounded-md bg-white/10 px-3 py-1.5 text-xs font-black uppercase text-brand-100 ring-1 ring-white/20 backdrop-blur">
                    Academia Desportiva da Mealhada
                </span>
                <h1 class="mx-auto mt-6 max-w-full text-[clamp(2.85rem,7vw,7rem)] font-black uppercase leading-none text-white lg:mx-0">
                    AD Mealhada
                </h1>
                <p class="mt-2 text-[clamp(2rem,4.8vw,5.25rem)] font-black uppercase leading-none text-brand-200">
                    Basquetebol
                </p>
                <p class="mx-auto mt-6 max-w-2xl text-base font-medium leading-7 text-white/82 sm:text-xl sm:leading-8 lg:mx-0">
                    Clube de formação, competição e comunidade. Um projeto jovem, ambicioso e feito para crescer dentro e fora do campo.
                </p>
                <div class="mt-8 grid gap-3 sm:flex sm:flex-wrap sm:items-center sm:justify-center lg:justify-start">
                    <a href="{{ route('equipas') }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-white px-5 py-3 text-sm font-black uppercase text-brand-900 shadow-xl transition hover:-translate-y-0.5 hover:bg-brand-50 focus:outline-none focus:ring-2 focus:ring-white/60">
                        Conhecer as equipas
                    </a>
                    <a href="{{ route('calendario') }}" class="inline-flex min-h-12 items-center justify-center rounded-md border border-white/25 bg-white/10 px-5 py-3 text-sm font-black uppercase text-white shadow-sm backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/18 focus:outline-none focus:ring-2 focus:ring-white/50">
                        Calendário de jogos
                    </a>
                </div>

                <div class="mt-9 grid grid-cols-3 overflow-hidden rounded-lg border border-white/15 bg-white/10 text-left shadow-2xl backdrop-blur">
                    <div class="border-r border-white/10 p-4">
                        <p class="text-3xl font-black text-white">{{ $totalEquipas }}</p>
                        <p class="mt-1 text-[11px] font-black uppercase text-brand-100/80">Equipas</p>
                    </div>
                    <div class="p-4">
                        <p class="text-3xl font-black text-white">{{ $totalJogadores }}</p>
                        <p class="mt-1 text-[11px] font-black uppercase text-brand-100/80">Jogadores</p>
                    </div>
                    <div class="border-r border-white/10 p-4">
                        <p class="text-3xl font-black text-white">{{ $totalPatrocinadores }}</p>
                        <p class="mt-1 text-[11px] font-black uppercase text-brand-100/80">Patrocinadores</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden border-y border-brand-100 bg-white text-brand-800">
        <div class="kinetic-track py-3 text-sm font-black uppercase opacity-90">
            <div class="kinetic-group">
                @foreach (['Minibasquete', 'Formação', 'Competição', 'Mealhada', 'Basquetebol', 'AD Mealhada'] as $item)
                    <span class="px-2">{{ $item }}</span>
                @endforeach
            </div>
            <div class="kinetic-group" aria-hidden="true">
                @foreach (['Minibasquete', 'Formação', 'Competição', 'Mealhada', 'Basquetebol', 'AD Mealhada'] as $item)
                    <span class="px-2">{{ $item }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Jogos --}}
    <section class="bg-white py-12 sm:py-16 dark:bg-canvas">
        <div class="site-shell">
            <div class="space-y-12">
                <div class="rounded-lg border border-line bg-surface-2 p-4 sm:p-6">
                    <x-site.section-heading title="Próximos jogos" :link="route('calendario')" link-label="Ver calendário" />
                    @if ($proximosJogos->isEmpty())
                        <p class="site-card border-dashed p-6 text-sm text-muted">Não existem jogos agendados.</p>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($proximosJogos as $game)
                                <x-site.game-card :game="$game" />
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-lg border border-line bg-surface-2 p-4 sm:p-6">
                    <x-site.section-heading title="Últimos resultados" :link="route('calendario')" link-label="Ver todos" />
                    @if ($ultimosResultados->isEmpty())
                        <p class="site-card border-dashed p-6 text-sm text-muted">Resultados a sincronizar pela API.</p>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($ultimosResultados as $game)
                                <x-site.game-card :game="$game" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Notícias --}}
    <section class="bg-surface py-14 text-ink">
        <div class="site-shell">
            <x-site.section-heading title="Notícias e novidades" subtitle="As novidades, os resultados e as histórias que fazem o nosso clube." :link="route('noticias')" />

            <div class="grid gap-6 lg:grid-cols-3">
                @if ($destaque)
                    <a href="{{ route('noticias.show', $destaque) }}" class="site-card group relative col-span-1 flex min-h-80 flex-col justify-end lg:col-span-2 lg:row-span-2">
                        <x-site.media :src="$destaque->capa" :seed="$destaque->id" :icon="false" class="absolute inset-0" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/35 to-transparent"></div>
                        <div class="relative p-6 lg:p-8">
                            <span class="rounded-md bg-rim-500 px-3 py-1 text-xs font-black uppercase text-white">{{ $destaque->categoria }}</span>
                            <h3 class="mt-3 text-2xl font-black uppercase leading-tight text-white lg:text-4xl">{{ $destaque->titulo }}</h3>
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
    <section class="bg-white py-14 dark:bg-canvas">
        <div class="site-shell">
        <x-site.section-heading title="As nossas equipas" subtitle="Conhece quem representa as nossas cores em cada escalão." :link="route('equipas')" />
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($equipas as $team)
                <a href="{{ $team->publicShowUrl() }}" class="site-card group relative flex aspect-square flex-col justify-end hover:-translate-y-1">
                    <x-site.media :src="$team->foto" :seed="$team->id" :icon="false" class="absolute inset-0 transition duration-300 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/30 to-transparent"></div>
                    <div class="relative p-4">
                        <span class="text-xs font-black uppercase text-rim-200">{{ $team->escalao }}</span>
                        <h3 class="text-base font-black uppercase leading-tight text-white">{{ $team->nome }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
        </div>
    </section>

    {{-- Galeria --}}
    @if ($albuns->isNotEmpty())
        <section class="bg-surface py-14">
            <div class="site-shell">
                <x-site.section-heading title="Galeria de Fotos" subtitle="Revive os melhores momentos, dentro e fora de campo." :link="route('galeria')" link-label="Ver galeria" />
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($albuns as $album)
                        <a href="{{ route('galeria.show', $album) }}" class="site-card group relative aspect-[4/5] hover:-translate-y-1">
                            <x-site.media :src="$album->capa" :seed="$album->id" :icon="false" class="absolute inset-0 transition duration-500 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/90 via-brand-950/35 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-4">
                                <span class="rounded-md bg-white/90 px-2.5 py-1 text-xs font-black uppercase text-brand-800">{{ $album->photos_count }} fotos</span>
                                <h3 class="mt-3 text-base font-black uppercase leading-tight text-white">{{ $album->titulo }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Modalidades --}}
    @if ($modalidades->isNotEmpty())
        <section class="site-shell py-14">
            <x-site.section-heading title="Modalidades" subtitle="As modalidades desportivas do clube." />
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($modalidades as $modalidade)
                    <div class="site-card p-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-rim-500 text-white">
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

    {{-- CTA --}}
    <section class="bg-white py-14 dark:bg-zinc-950">
        <div class="site-shell">
            <div class="glass-panel flex flex-col items-center justify-between gap-6 rounded-lg border border-brand-100 p-6 text-center lg:flex-row lg:p-8 lg:text-left">
            <div>
                <h2 class="text-2xl font-black uppercase text-ink sm:text-3xl">Queres jogar pela AD Mealhada?</h2>
                <p class="mt-2 text-body">Inscrições abertas para todos os escalões de formação. Vem treinar connosco!</p>
            </div>
            <a href="{{ route('contactos') }}" class="btn-primary w-full shrink-0 sm:w-auto">
                Contacta-nos
            </a>
            </div>
        </div>
    </section>
</div>
