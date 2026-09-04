<div>
    <x-site.page-header
        eyebrow="Basquetebol"
        title="As nossas equipas"
        subtitle="Conhece quem representa as nossas cores em cada escalão." />

    <section class="site-shell py-14">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($equipas as $team)
                <a href="{{ route('equipas.show', $team) }}" class="site-card group flex flex-col hover:-translate-y-1">
                    <div class="relative aspect-[4/3] overflow-hidden bg-brand-950">
                        <x-site.media
                            :src="$team->foto"
                            :seed="$team->id"
                            :icon="false"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-950/60 via-brand-950/25 to-transparent"></div>
                        <span class="absolute left-4 top-4 rounded-md bg-rim-500 px-3 py-1 text-xs font-black uppercase text-white shadow-sm">{{ $team->escalao }}</span>
                        <div class="absolute inset-x-0 bottom-0 p-4 pt-10">
                            <h2 class="text-lg font-black uppercase text-white drop-shadow-sm">{{ $team->nome }}</h2>
                            <p class="mt-0.5 text-sm font-medium text-brand-200">{{ $team->generoLabel() }}</p>
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        @if ($team->descricao)
                            <p class="line-clamp-2 flex-1 text-sm leading-6 text-body">{{ $team->descricao }}</p>
                        @endif
                        <div @class(['flex items-center justify-between text-xs text-muted', 'mt-4 border-t border-line pt-4' => $team->descricao, 'mt-auto' => ! $team->descricao])>
                            <span>{{ $team->jogadores_count }} atletas</span>
                            @if ($team->competicao)
                                <span class="truncate text-right">{{ \Illuminate\Support\Str::limit($team->competicao, 28) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</div>
