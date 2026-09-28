<div>
    <x-admin.header title="Jogadores" subtitle="Plantéis e equipa técnica">
        <flux:button variant="primary" icon="plus"
            :href="route('admin.players.create', $team !== '' ? ['team' => $team] : [])" wire:navigate>Novo jogador</flux:button>
    </x-admin.header>

    <div class="mb-4 max-w-xs">
        <flux:select wire:model.live="team" label="Filtrar por equipa">
            <flux:select.option value="">Todas as equipas</flux:select.option>
            @foreach ($teams as $t)
                <flux:select.option value="{{ $t->id }}">{{ $t->adminLabel() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Nascimento</th>
                    <th class="px-4 py-3">Equipa</th>
                    <th class="px-4 py-3">Posição / Papel</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($players as $player)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3 font-mono text-zinc-500">{{ $player->numero ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 shrink-0 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    @if ($player->foto)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($player->foto) }}" class="h-full w-full object-cover" alt="">
                                    @endif
                                </div>
                                <span class="font-semibold text-zinc-900 dark:text-white">{{ $player->nome }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $player->data_nascimento?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $player->team?->nome }}</td>
                        <td class="px-4 py-3">
                            <span class="text-zinc-600 dark:text-zinc-300">{{ $player->posicao ?: '—' }}</span>
                            @if ($player->papel !== 'jogador')
                                <flux:badge color="purple" size="sm" class="ml-1">{{ ucfirst(str_replace('-', ' ', $player->papel)) }}</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.players.edit', $player)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $player->id }})"
                                    wire:confirm="Eliminar '{{ $player->nome }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-zinc-500">Sem jogadores para o filtro selecionado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
