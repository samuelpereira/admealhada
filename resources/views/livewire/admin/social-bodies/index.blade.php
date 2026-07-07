<div>
    <x-admin.header title="Órgãos sociais" subtitle="Direção, Assembleia Geral e Conselho Fiscal">
        <flux:button variant="primary" icon="plus" :href="route('admin.social-bodies.create')" wire:navigate>Novo membro</flux:button>
    </x-admin.header>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Categoria</th>
                    <th class="px-4 py-3">Cargo</th>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Ordem</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($socialBodies as $membro)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3">
                            <flux:badge color="blue" size="sm">{{ $categorias[$membro->categoria] ?? $membro->categoria }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">{{ $membro->cargo }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $membro->nome }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $membro->ordem }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggle({{ $membro->id }})" type="button">
                                <flux:badge :color="$membro->ativo ? 'green' : 'zinc'" size="sm">
                                    {{ $membro->ativo ? 'Ativo' : 'Inativo' }}
                                </flux:badge>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.social-bodies.edit', $membro)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $membro->id }})"
                                    wire:confirm="Eliminar '{{ $membro->cargo }} — {{ $membro->nome }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-zinc-500">Ainda não há membros dos órgãos sociais.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
