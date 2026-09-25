<div>
    <x-admin.header :title="$user ? 'Editar utilizador' : 'Novo utilizador'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.users.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="max-w-2xl space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <flux:input wire:model="name" label="Nome" required autofocus />
        <flux:input wire:model="email" label="Email" type="email" required />
        <flux:input wire:model="password" label="Palavra-passe" type="password" :required="! $user" autocomplete="new-password" viewable
            :description="$user ? 'Deixa em branco para manter a palavra-passe actual.' : null" />
        <flux:input wire:model="password_confirmation" label="Confirmar palavra-passe" type="password" :required="! $user" autocomplete="new-password" viewable />
        <flux:checkbox wire:model="isAdmin" label="Administrador" description="Pode entrar no backoffice." />
        @error('isAdmin')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror

        <div class="flex items-center gap-3 border-t border-zinc-100 pt-4 dark:border-zinc-800">
            <flux:button variant="primary" type="submit">Guardar</flux:button>
            <flux:button variant="ghost" :href="route('admin.users.index')" wire:navigate>Cancelar</flux:button>
        </div>
    </form>
</div>
