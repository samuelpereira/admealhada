<div>
    @push('structured-data')
        <script type="application/ld+json">@json($teamSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)</script>
    @endpush

    <x-site.page-header
        :eyebrow="$team->escalao.' · '.$team->generoLabel().($team->season && ! $team->season->is_current ? ' · '.$team->season->name : '')"
        :title="$team->nome"
        :subtitle="$team->competicao"
        :image="$team->foto"
        :seed="$team->id">
        <a href="{{ $team->publicIndexUrl() }}" class="mt-6 inline-flex min-h-10 items-center gap-1 text-sm font-black text-brand-200 hover:text-white">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Todas as equipas
        </a>
    </x-site.page-header>

    <section class="site-shell py-12">
        <div class="grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                @if ($team->descricao)
                    <p class="text-lg leading-relaxed text-body">{{ $team->descricao }}</p>
                @endif

                {{-- Plantel --}}
                <div class="mt-10">
                    <h2 class="mb-6 flex items-center gap-3 text-2xl font-black uppercase text-ink">
                        <span class="h-1.5 w-8 rounded-full bg-rim-500"></span>
                        Plantel
                    </h2>

                    @if ($jogadores->isEmpty())
                        <p class="site-card border-dashed p-6 text-sm text-muted">Plantel a anunciar em breve.</p>
                    @else
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($jogadores as $jogador)
                                <div class="site-card">
                                    <div class="relative aspect-square overflow-hidden">
                                        <x-site.media :src="$jogador->foto" :seed="$jogador->id" :icon="false" />
                                        @if ($jogador->numero)
                                            <span class="absolute left-2 top-2 flex h-9 w-9 items-center justify-center rounded-md bg-rim-500 text-sm font-black text-white">{{ $jogador->numero }}</span>
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
                        <h3 class="mb-4 mt-10 text-lg font-black uppercase text-ink">Equipa técnica</h3>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($staff as $membro)
                                <div class="site-card flex items-center gap-3 p-3 pr-5">
                                    <div class="h-12 w-12 overflow-hidden rounded-full">
                                        <x-site.media :src="$membro->foto" :seed="$membro->id" :icon="false" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-ink">{{ $membro->nome }}</p>
                                        <p class="text-xs font-black uppercase text-rim-600 dark:text-rim-300">{{ $membro->papel }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if ($team->coaches->isNotEmpty())
                    <div class="mt-10">
                        <h2 class="mb-6 flex items-center gap-3 text-2xl font-black uppercase text-ink">
                            <span class="h-1.5 w-8 rounded-full bg-rim-500"></span>
                            Treinadores
                        </h2>
                        <div class="flex flex-wrap gap-4">
                            @foreach ($team->coaches as $treinador)
                                <div class="site-card flex items-center gap-3 p-3 pr-5">
                                    <div class="h-12 w-12 overflow-hidden rounded-full">
                                        <x-site.media :src="$treinador->foto" :seed="$treinador->id" :icon="false" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-ink">{{ $treinador->nome }}</p>
                                        @if ($treinador->grau)
                                            <p class="text-xs font-black uppercase text-rim-600 dark:text-rim-300">{{ $treinador->grau }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar jogos --}}
            <div class="space-y-10">
                @if ($team->sponsors->isNotEmpty())
                    <div>
                        <h2 class="mb-4 text-lg font-bold text-ink">Patrocinadores da Equipa</h2>
                        <div @class([
                            'grid grid-cols-3 gap-3' => $team->sponsors->count() > 1,
                        ])>
                            @foreach ($team->sponsors as $sponsor)
                                <x-site.sponsor-card :sponsor="$sponsor" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <h2 class="mb-4 text-lg font-bold text-ink">Próximos jogos</h2>
                    @if ($proximosJogos->isEmpty())
                        <p class="site-card border-dashed p-5 text-sm text-muted">Sem jogos agendados.</p>
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
                        <p class="site-card border-dashed p-5 text-sm text-muted">Ainda sem resultados.</p>
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
