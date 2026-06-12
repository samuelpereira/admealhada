<div>
    <x-admin.header title="Modalidades" subtitle="Modalidades desportivas do clube">
        <flux:button variant="primary" icon="plus" :href="route('admin.sports.create')" wire:navigate>Nova modalidade</flux:button>
    </x-admin.header>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Equipas</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($sports as $sport)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $sport->nome }}</div>
                            @if ($sport->descricao)
                                <div class="text-xs text-zinc-500">{{ \Illuminate\Support\Str::limit($sport->descricao, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $sport->teams_count }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggle({{ $sport->id }})" type="button">
                                <flux:badge :color="$sport->ativo ? 'green' : 'zinc'" size="sm">
                                    {{ $sport->ativo ? 'Ativa' : 'Inativa' }}
                                </flux:badge>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.sports.edit', $sport)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $sport->id }})"
                                    wire:confirm="Eliminar a modalidade '{{ $sport->nome }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-zinc-500">Ainda não há modalidades.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
