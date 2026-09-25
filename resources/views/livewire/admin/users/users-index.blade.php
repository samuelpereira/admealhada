<div>
    <x-admin.header title="Utilizadores" subtitle="Contas com acesso ao backoffice">
        <flux:button variant="primary" icon="plus" :href="route('admin.users.create')" wire:navigate>Novo utilizador</flux:button>
    </x-admin.header>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Acesso</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($users as $user)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40" wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$user->is_admin ? 'green' : 'zinc'" size="sm">
                                {{ $user->is_admin ? 'Administrador' : 'Sem acesso' }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.users.edit', $user)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete({{ $user->id }})"
                                    wire:confirm="Eliminar o utilizador '{{ $user->name }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-zinc-500">Ainda não há utilizadores.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
