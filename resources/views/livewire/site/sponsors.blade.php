<div>
    <x-site.page-header
        eyebrow="Apoios"
        title="Patrocinadores"
        subtitle="Empresas e entidades que apoiam o nosso clube e as nossas equipas." />

    @if ($equipas->isNotEmpty())
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <x-site.section-heading title="Patrocinadores principais" subtitle="Patrocinadores associados a cada equipa." />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($equipas as $team)
                    <div class="flex flex-col rounded-xl border border-line bg-surface p-4">
                        <div class="flex-1">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-300">{{ $team->escalao }}</p>
                            <h2 class="mt-0.5 text-base font-extrabold text-ink">{{ $team->nome }}</h2>
                            @if ($team->competicao)
                                <p class="mt-0.5 line-clamp-2 text-sm text-muted">{{ $team->competicao }}</p>
                            @endif
                        </div>

                        <div class="mt-4 space-y-3">
                            @foreach ($team->sponsors as $sponsor)
                                <x-site.sponsor-card :sponsor="$sponsor" />
                            @endforeach
                        </div>

                        <a href="{{ route('equipas.show', $team) }}" class="mt-4 text-sm font-semibold text-brand-700 hover:underline dark:text-brand-300">
                            Ver equipa
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($patrocinadoresGerais->isNotEmpty())
        <section @class([
            'bg-surface py-14' => $equipas->isNotEmpty(),
            'mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8' => $equipas->isEmpty(),
        ])>
            <div @class(['mx-auto max-w-7xl px-4 sm:px-6 lg:px-8' => $equipas->isNotEmpty()])>
                <x-site.section-heading title="Patrocinadores gerais" subtitle="Parceiros que apoiam o clube na globalidade." />

                <div class="grid grid-cols-3 gap-3">
                    @foreach ($patrocinadoresGerais as $sponsor)
                        <x-site.sponsor-card :sponsor="$sponsor" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($equipas->isEmpty() && $patrocinadoresGerais->isEmpty())
        <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="rounded-xl border border-dashed border-line bg-surface p-8 text-center text-muted">
                Ainda não há patrocinadores publicados.
            </p>
        </section>
    @endif
</div>
