<div>
    <x-admin.header title="Jogos" subtitle="Calendário e resultados">
        <flux:button variant="primary" icon="plus" :href="route('admin.games.create')" wire:navigate>Novo jogo</flux:button>
    </x-admin.header>

    <div class="mb-4 flex flex-wrap gap-4">
        @if ($epocas->isNotEmpty())
            <flux:select wire:model.live="epoca" label="Época" class="max-w-xs">
                @foreach ($epocas as $epoca)
                    <flux:select.option value="{{ $epoca->slug }}">{{ $epoca->name }}{{ $epoca->is_current ? ' (actual)' : '' }}</flux:select.option>
                @endforeach
            </flux:select>
        @endif

        <flux:select wire:model.live="team" label="Equipa" class="max-w-xs">
            <flux:select.option value="">Todas as equipas</flux:select.option>
            @foreach ($teams as $t)
                <flux:select.option value="{{ $t->id }}">{{ $t->adminLabel() }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="estado" label="Estado" class="max-w-xs">
            <flux:select.option value="">Todos os estados</flux:select.option>
            <flux:select.option value="agendado">Agendado</flux:select.option>
            <flux:select.option value="terminado">Terminado</flux:select.option>
            <flux:select.option value="adiado">Adiado</flux:select.option>
            <flux:select.option value="cancelado">Cancelado</flux:select.option>
        </flux:select>

        <flux:input wire:model.live.debounce.300ms="pesquisa" label="Equipa ou adversário" placeholder="Pesquisar" class="max-w-xs" />
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Jogo</th>
                    <th class="px-4 py-3">Resultado</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($games as $game)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $game->data_hora?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-zinc-900 dark:text-white">
                                @if ($game->emCasa())
                                    {{ $game->team?->nome }} <span class="text-zinc-400">vs</span> {{ $game->adversario }}
                                @else
                                    {{ $game->adversario }} <span class="text-zinc-400">vs</span> {{ $game->team?->nome }}
                                @endif
                            </div>
                            <div class="text-xs text-zinc-500">{{ $game->competicao }}@if ($game->jornada) · J{{ $game->jornada }}@endif · {{ $game->emCasa() ? 'Casa' : 'Fora' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-zinc-700 dark:text-zinc-200">
                            @if ($game->estado === 'terminado' && $game->pontos_casa !== null)
                                {{ $game->pontos_casa }} - {{ $game->pontos_fora }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$game->estadoColor()" size="sm">{{ ucfirst($game->estado) }}</flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.games.edit', $game)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $game->id }})"
                                    wire:confirm="Eliminar este jogo?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-500">Sem jogos para o filtro selecionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $games->links() }}
    </div>
</div>
