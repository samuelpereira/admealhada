<div>
    <x-admin.header title="Treinadores" subtitle="Treinadores do clube e as equipas que acompanham">
        <flux:button variant="primary" icon="plus" :href="route('admin.coaches.create')" wire:navigate>Novo treinador</flux:button>
    </x-admin.header>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Foto</th>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Nascimento</th>
                    <th class="px-4 py-3">Nacionalidade</th>
                    <th class="px-4 py-3">Grau</th>
                    <th class="px-4 py-3">Equipas</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($coaches as $coach)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3">
                            <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                @if ($coach->foto)
                                    <img src="{{ \App\Support\MediaUrl::resolve($coach->foto) }}" alt="{{ $coach->nome }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">{{ $coach->nome }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $coach->data_nascimento?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $coach->nacionalidade ?: '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $coach->grau ?: '—' }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            @if ($coach->teams->isNotEmpty())
                                {{ $coach->teams->map->adminLabel()->join(', ') }}
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.coaches.edit', $coach)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $coach->id }})"
                                    wire:confirm="Eliminar o treinador '{{ $coach->nome }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-zinc-500">Ainda não há treinadores.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
