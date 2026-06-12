<div>
    <x-site.page-header
        :eyebrow="$team->escalao.' · '.$team->generoLabel()"
        :title="$team->nome"
        :subtitle="$team->competicao"
        :image="$team->foto"
        :seed="$team->id">
        <a href="{{ route('equipas') }}" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-brand-200 hover:text-white">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Todas as equipas
        </a>
    </x-site.page-header>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                @if ($team->descricao)
                    <p class="text-lg leading-relaxed text-body">{{ $team->descricao }}</p>
                @endif

                {{-- Plantel --}}
                <div class="mt-10">
                    <h2 class="mb-6 flex items-center gap-2 text-2xl font-extrabold text-ink">
                        <span class="h-6 w-1.5 rounded-full bg-brand-600"></span>
                        Plantel
                    </h2>

                    @if ($jogadores->isEmpty())
                        <p class="rounded-xl border border-dashed border-line bg-surface p-6 text-sm text-muted">Plantel a anunciar em breve.</p>
                    @else
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($jogadores as $jogador)
                                <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-sm">
                                    <div class="relative aspect-square overflow-hidden">
                                        <x-site.media :src="$jogador->foto" :seed="$jogador->id" :icon="false" />
                                        @if ($jogador->numero)
                                            <span class="absolute left-2 top-2 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-sm font-black text-brand-800">{{ $jogador->numero }}</span>
                                        @endif
                                    </div>
                                    <div class="p-3">
                                        <p class="truncate text-sm font-bold text-ink">{{ $jogador->nome }}</p>
                                        <p class="text-xs text-muted">{{ $jogador->posicao }}@if ($jogador->ano_nascimento) · {{ $jogador->ano_nascimento }}@endif</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($staff->isNotEmpty())
                        <h3 class="mb-4 mt-10 text-lg font-bold text-ink">Equipa técnica</h3>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($staff as $membro)
                                <div class="flex items-center gap-3 rounded-xl border border-line bg-surface p-3 pr-5 shadow-sm">
                                    <div class="h-12 w-12 overflow-hidden rounded-full">
                                        <x-site.media :src="$membro->foto" :seed="$membro->id" :icon="false" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-ink">{{ $membro->nome }}</p>
                                        <p class="text-xs text-brand-600 dark:text-brand-300">{{ $membro->papel }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar jogos --}}
            <div class="space-y-10">
                <div>
                    <h2 class="mb-4 text-lg font-bold text-ink">Próximos jogos</h2>
                    @if ($proximosJogos->isEmpty())
                        <p class="rounded-xl border border-dashed border-line bg-surface p-5 text-sm text-muted">Sem jogos agendados.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($proximosJogos as $game)
                                <x-site.game-card :game="$game" :show-team="false" />
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <h2 class="mb-4 text-lg font-bold text-ink">Últimos resultados</h2>
                    @if ($resultados->isEmpty())
                        <p class="rounded-xl border border-dashed border-line bg-surface p-5 text-sm text-muted">Ainda sem resultados.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($resultados as $game)
                                <x-site.game-card :game="$game" :show-team="false" />
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
