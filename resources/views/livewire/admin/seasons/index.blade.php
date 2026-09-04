<div>
    <x-admin.header title="Épocas" subtitle="Épocas desportivas do clube">
        <flux:button variant="primary" icon="plus" :href="route('admin.seasons.create')" wire:navigate>Nova época</flux:button>
    </x-admin.header>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Época</th>
                    <th class="px-4 py-3">Período</th>
                    <th class="px-4 py-3">Equipas</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($epocas as $epoca)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40" wire:key="season-{{ $epoca->id }}">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $epoca->name }}</div>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $epoca->periodLabel() }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $epoca->teams_count }}</td>
                        <td class="px-4 py-3">
                            @if ($epoca->is_current)
                                <flux:badge color="green" size="sm">Actual</flux:badge>
                            @else
                                <flux:button size="xs" variant="subtle"
                                    wire:click="markCurrent({{ $epoca->id }})"
                                    wire:confirm="Marcar {{ $epoca->name }} como época actual?">Marcar como actual</flux:button>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.seasons.edit', $epoca)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $epoca->id }})"
                                    wire:confirm="Eliminar a época '{{ $epoca->name }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-500">Ainda não há épocas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
