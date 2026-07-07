<div>
    <x-admin.header :title="$socialBody ? 'Editar membro' : 'Novo membro'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.social-bodies.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="max-w-2xl">
        <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <flux:select wire:model="categoria" label="Categoria" required>
                @foreach ($categorias as $valor => $label)
                    <flux:select.option value="{{ $valor }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="cargo" label="Cargo" placeholder="Presidente, Secretário, Vogal..." required autofocus />
            <flux:input wire:model="nome" label="Nome" required />
            <flux:input wire:model="ordem" label="Ordem" type="number" min="0"
                description="Membros com ordem mais baixa aparecem primeiro dentro da categoria." />
            <flux:checkbox wire:model="ativo" label="Ativo no site" />
        </div>

        <div class="mt-6 flex items-center gap-3">
            <flux:button variant="primary" type="submit">Guardar</flux:button>
            <flux:button variant="ghost" :href="route('admin.social-bodies.index')" wire:navigate>Cancelar</flux:button>
        </div>
    </form>
</div>
