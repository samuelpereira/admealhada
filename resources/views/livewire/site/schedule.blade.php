<div>
    <x-site.page-header
        :eyebrow="'Época '.$epoca"
        title="Calendário e resultados"
        subtitle="Acompanha todos os jogos das equipas da AD Mealhada." />

    <section class="site-shell py-12">
        {{-- Filtros --}}
        <div class="site-card mb-8 grid gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:flex lg:items-end">
            <div class="min-w-0">
                <label for="epoca" class="mb-1.5 block text-sm font-semibold text-body">Época</label>
                <select id="epoca" wire:model.live="epoca" class="min-h-11 w-full rounded-md border-line bg-surface py-2 pl-3 pr-10 text-sm font-medium text-body shadow-sm focus:border-brand-500 focus:ring-brand-500 lg:min-w-[10rem]">
                    @foreach ($epocas as $opcao)
                        <option value="{{ $opcao }}">{{ $opcao }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0">
                <label for="equipa" class="mb-1.5 block text-sm font-semibold text-body">Equipa</label>
                <select id="equipa" wire:model.live="teamId" class="min-h-11 w-full rounded-md border-line bg-surface py-2 pl-3 pr-10 text-sm font-medium text-body shadow-sm focus:border-brand-500 focus:ring-brand-500 lg:min-w-[12rem]">
                    <option value="">Todas as equipas</option>
                    @foreach ($equipas as $team)
                        <option value="{{ $team->id }}">{{ $team->nome }}</option>
                    @endforeach
                </select>
            </div>

            @if ($competicoes !== [])
                <div class="min-w-0">
                    <label for="competicao" class="mb-1.5 block text-sm font-semibold text-body">Competição</label>
                    <select id="competicao" wire:model.live="competicaoFilter" class="min-h-11 w-full rounded-md border-line bg-surface py-2 pl-3 pr-10 text-sm font-medium text-body shadow-sm focus:border-brand-500 focus:ring-brand-500 lg:min-w-[12rem]">
                        <option value="">Todas as competições</option>
                        @foreach ($competicoes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="space-y-12">
            <div>
                <x-site.section-heading title="Próximos jogos" />
                @if ($proximos->isEmpty())
                    <p class="site-card border-dashed p-6 text-sm text-muted">Sem jogos agendados para esta seleção.</p>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($proximos as $game)
                            <x-site.game-card :game="$game" :show-team="$teamId === ''" />
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <x-site.section-heading title="Resultados" />
                @if ($resultados->isEmpty())
                    <p class="site-card border-dashed p-6 text-sm text-muted">Sem resultados para esta seleção.</p>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($resultados as $game)
                            <x-site.game-card :game="$game" :show-team="$teamId === ''" />
                        @endforeach
                    </div>

                    @if ($hasMoreResultados)
                        <div class="mt-8 text-center">
                            <button
                                type="button"
                                wire:click="loadMoreResultados"
                                wire:loading.attr="disabled"
                                class="min-h-11 rounded-md bg-surface px-6 py-2.5 text-sm font-black text-body ring-1 ring-line transition hover:bg-surface-2 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="loadMoreResultados">Carregar mais</span>
                                <span wire:loading wire:target="loadMoreResultados">A carregar…</span>
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>
</div>
