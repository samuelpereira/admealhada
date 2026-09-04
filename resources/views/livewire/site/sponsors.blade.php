<div>
    <x-site.page-header
        eyebrow="Apoios"
        title="Patrocinadores"
        subtitle="Empresas e entidades que apoiam o nosso clube e as nossas equipas." />

    @if ($equipas->isNotEmpty())
        <section class="site-shell py-14">
            <x-site.section-heading title="Patrocinadores principais" subtitle="Patrocinadores associados a cada equipa." />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($equipas as $team)
                    <div class="site-card flex flex-col p-4">
                        <div class="flex-1">
                            <p class="text-xs font-black uppercase text-rim-600 dark:text-rim-300">{{ $team->escalao }}</p>
                            <h2 class="mt-0.5 text-base font-black uppercase text-ink">{{ $team->nome }}</h2>
                            @if ($team->competicao)
                                <p class="mt-0.5 line-clamp-2 text-sm text-muted">{{ $team->competicao }}</p>
                            @endif
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach ($team->sponsors as $sponsor)
                                <x-site.sponsor-card :sponsor="$sponsor" />
                            @endforeach
                        </div>

                        <a href="{{ $team->publicShowUrl() }}" class="mt-4 inline-flex min-h-10 items-center text-sm font-black text-brand-700 hover:text-rim-600 dark:text-brand-300">
                            Ver equipa
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($patrocinadoresGerais->isNotEmpty())
        <section @class([
            'bg-surface/80 py-14' => $equipas->isNotEmpty(),
            'site-shell py-14' => $equipas->isEmpty(),
        ])>
            <div @class(['site-shell' => $equipas->isNotEmpty()])>
                <x-site.section-heading title="Patrocinadores gerais" subtitle="Parceiros que apoiam o clube na globalidade." />

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                    @foreach ($patrocinadoresGerais as $sponsor)
                        <x-site.sponsor-card :sponsor="$sponsor" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($equipas->isEmpty() && $patrocinadoresGerais->isEmpty())
        <section class="site-shell py-14">
            <p class="site-card border-dashed p-8 text-center text-muted">
                Ainda não há patrocinadores publicados.
            </p>
        </section>
    @endif
</div>
