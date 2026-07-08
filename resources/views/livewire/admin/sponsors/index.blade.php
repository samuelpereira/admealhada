<div>
    <x-admin.header title="Patrocinadores" subtitle="Logótipos e links dos patrocinadores do clube">
        <flux:button variant="primary" icon="plus" :href="route('admin.sponsors.create')" wire:navigate>Novo patrocinador</flux:button>
    </x-admin.header>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Logo</th>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Equipas</th>
                    <th class="px-4 py-3">Link</th>
                    <th class="px-4 py-3">Ordem</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($sponsors as $sponsor)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3">
                            <div class="flex h-12 w-24 items-center justify-center overflow-hidden rounded border border-zinc-200 bg-white p-1 dark:border-zinc-700 dark:bg-zinc-800">
                                @if ($sponsor->logo)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($sponsor->logo) }}" alt="{{ $sponsor->nome }}" class="max-h-full max-w-full object-contain">
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">{{ $sponsor->nome }}</td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$sponsor->tipo === 'principal' ? 'blue' : 'zinc'" size="sm">
                                {{ $sponsor->tipoLabel() }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">
                            @if ($sponsor->teams->isNotEmpty())
                                {{ $sponsor->teams->pluck('nome')->join(', ') }}
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($sponsor->link)
                                <a href="{{ $sponsor->link }}" target="_blank" rel="noopener noreferrer" class="text-brand-600 hover:underline dark:text-brand-300">
                                    {{ \Illuminate\Support\Str::limit($sponsor->link, 40) }}
                                </a>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $sponsor->ordem }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggle({{ $sponsor->id }})" type="button">
                                <flux:badge :color="$sponsor->ativo ? 'green' : 'zinc'" size="sm">
                                    {{ $sponsor->ativo ? 'Ativo' : 'Inativo' }}
                                </flux:badge>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.sponsors.edit', $sponsor)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $sponsor->id }})"
                                    wire:confirm="Eliminar o patrocinador '{{ $sponsor->nome }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-zinc-500">Ainda não há patrocinadores.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
