<div>
    <x-admin.header :title="$sport ? 'Editar modalidade' : 'Nova modalidade'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.sports.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="max-w-2xl space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <flux:input wire:model="nome" label="Nome" required autofocus />
        <flux:textarea wire:model="descricao" label="Descrição" rows="4" />
        <flux:checkbox wire:model="ativo" label="Modalidade ativa" />

        <div class="flex items-center gap-3 border-t border-zinc-100 pt-4 dark:border-zinc-800">
            <flux:button variant="primary" type="submit">Guardar</flux:button>
            <flux:button variant="ghost" :href="route('admin.sports.index')" wire:navigate>Cancelar</flux:button>
        </div>
    </form>
</div>
