<div>
    <x-admin.header title="Equipas" subtitle="Escalões e equipas do clube">
        <flux:button variant="primary" icon="plus" :href="route('admin.teams.create')" wire:navigate>Nova equipa</flux:button>
    </x-admin.header>

    @if ($epocas->isNotEmpty())
        <div class="mb-4 max-w-xs">
            <flux:select wire:model.live="epoca" label="Época">
                @foreach ($epocas as $epoca)
                    <flux:select.option value="{{ $epoca->slug }}">{{ $epoca->name }}{{ $epoca->is_current ? ' (actual)' : '' }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Equipa</th>
                    <th class="px-4 py-3">Modalidade</th>
                    <th class="px-4 py-3">Género</th>
                    <th class="px-4 py-3">Atletas</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($teams as $team)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40" wire:key="team-{{ $team->id }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 shrink-0 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    @if ($team->foto)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($team->foto) }}" class="h-full w-full object-cover" alt="">
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $team->nome }}</div>
                                    <div class="text-xs text-zinc-500">{{ $team->escalao }}@if ($team->competicao) · {{ $team->competicao }}@endif</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $team->sport?->nome }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $team->generoLabel() }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $team->players_count }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="users" :href="route('admin.players.index', ['team' => $team->id])" wire:navigate>Plantel</flux:button>
                                <flux:button size="xs" variant="subtle" icon="square-2-stack"
                                    wire:click="duplicate({{ $team->id }})"
                                    wire:confirm="Duplicar '{{ $team->nome }}' para a época seguinte? O plantel, os jogos e o link FPB não são copiados.">Duplicar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.teams.edit', $team)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $team->id }})"
                                    wire:confirm="Eliminar a equipa '{{ $team->nome }}'? Os jogadores associados também serão removidos.">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-500">Ainda não há equipas nesta época.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
