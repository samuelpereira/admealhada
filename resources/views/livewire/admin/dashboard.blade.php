<div>
    <x-admin.header title="Dashboard" subtitle="Visão geral do clube" />

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($cards as $card)
            <a href="{{ route($card['route']) }}" wire:navigate
               class="group rounded-xl border border-zinc-200 bg-white p-4 shadow-sm transition hover:border-brand-400 hover:shadow dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <flux:icon :icon="$card['icon']" class="h-6 w-6 text-brand-600 dark:text-brand-300" />
                    <flux:icon.arrow-up-right class="h-4 w-4 text-zinc-300 transition group-hover:text-brand-500" />
                </div>
                <div class="mt-3 text-3xl font-extrabold text-zinc-900 dark:text-white">{{ $card['value'] }}</div>
                <div class="text-sm text-zinc-500">{{ $card['label'] }}</div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="lg">Próximos jogos</flux:heading>
                <flux:link :href="route('admin.games.index')" wire:navigate class="text-sm">Ver todos</flux:link>
            </div>

            @forelse ($proximosJogos as $jogo)
                <div class="flex items-center justify-between border-t border-zinc-100 py-2.5 first:border-t-0 dark:border-zinc-800">
                    <div>
                        <div class="text-sm font-semibold text-zinc-900 dark:text-white">
                            {{ $jogo->team?->nome ?? 'Equipa' }} <span class="text-zinc-400">vs</span> {{ $jogo->adversario }}
                        </div>
                        <div class="text-xs text-zinc-500">{{ $jogo->data_hora?->format('d/m/Y H:i') }} · {{ $jogo->local }}</div>
                    </div>
                    <flux:button size="xs" variant="subtle" :href="route('admin.games.edit', $jogo)" wire:navigate>Editar</flux:button>
                </div>
            @empty
                <flux:text class="text-zinc-500">Sem jogos agendados.</flux:text>
            @endforelse
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <flux:heading size="lg">Notícias por publicar</flux:heading>
                <flux:link :href="route('admin.news.index')" wire:navigate class="text-sm">Ver todas</flux:link>
            </div>

            @forelse ($rascunhos as $noticia)
                <div class="flex items-center justify-between border-t border-zinc-100 py-2.5 first:border-t-0 dark:border-zinc-800">
                    <div>
                        <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $noticia->titulo }}</div>
                        <div class="text-xs text-zinc-500">
                            {{ $noticia->publicado_em ? 'Agendada · '.$noticia->publicado_em->format('d/m/Y H:i') : 'Rascunho' }}
                        </div>
                    </div>
                    <flux:button size="xs" variant="subtle" :href="route('admin.news.edit', $noticia)" wire:navigate>Editar</flux:button>
                </div>
            @empty
                <flux:text class="text-zinc-500">Sem rascunhos.</flux:text>
            @endforelse
        </div>
    </div>

    <div class="mt-6 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="mb-4">
            <flux:heading size="lg">Aniversários em {{ $mesAniversarios }}</flux:heading>
        </div>

        @forelse ($aniversarios as $jogador)
            <div class="flex items-center justify-between border-t border-zinc-100 py-2.5 first:border-t-0 dark:border-zinc-800">
                <div>
                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $jogador->nome }}</div>
                    <div class="text-xs text-zinc-500">{{ $jogador->team?->nome }} · {{ $jogador->data_nascimento?->format('d/m') }}</div>
                </div>
                <flux:button size="xs" variant="subtle" :href="route('admin.players.edit', $jogador)" wire:navigate>Editar</flux:button>
            </div>
        @empty
            <flux:text class="text-zinc-500">Nenhum aniversário este mês.</flux:text>
        @endforelse
    </div>
</div>
