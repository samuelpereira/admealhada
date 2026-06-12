<div>
    <x-site.page-header
        :eyebrow="'Época '.$epoca"
        title="Calendário e resultados"
        subtitle="Acompanha todos os jogos das equipas da AD Mealhada." />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        {{-- Filtros --}}
        <div class="mb-8 flex flex-wrap items-end gap-4">
            <div>
                <label for="epoca" class="mb-1.5 block text-sm font-semibold text-body">Época</label>
                <select id="epoca" wire:model.live="epoca" class="min-w-[10rem] rounded-lg border-line bg-surface py-2 pl-3 pr-10 text-sm font-medium text-body shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    @foreach ($epocas as $opcao)
                        <option value="{{ $opcao }}">{{ $opcao }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="equipa" class="mb-1.5 block text-sm font-semibold text-body">Equipa</label>
                <select id="equipa" wire:model.live="teamId" class="min-w-[12rem] rounded-lg border-line bg-surface py-2 pl-3 pr-10 text-sm font-medium text-body shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Todas as equipas</option>
                    @foreach ($equipas as $team)
                        <option value="{{ $team->id }}">{{ $team->nome }}</option>
                    @endforeach
                </select>
            </div>

            @if ($competicoes !== [])
                <div>
                    <label for="competicao" class="mb-1.5 block text-sm font-semibold text-body">Competição</label>
                    <select id="competicao" wire:model.live="competicaoFilter" class="min-w-[12rem] rounded-lg border-line bg-surface py-2 pl-3 pr-10 text-sm font-medium text-body shadow-sm focus:border-brand-500 focus:ring-brand-500">
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
                    <p class="rounded-xl border border-dashed border-line bg-surface p-6 text-sm text-muted">Sem jogos agendados para esta seleção.</p>
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
                    <p class="rounded-xl border border-dashed border-line bg-surface p-6 text-sm text-muted">Sem resultados para esta seleção.</p>
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
                                class="rounded-lg bg-surface px-6 py-2.5 text-sm font-semibold text-body ring-1 ring-line transition hover:bg-surface-2 disabled:opacity-50"
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
