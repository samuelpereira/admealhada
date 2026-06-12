<div>
    <x-admin.header :title="$player ? 'Editar jogador' : 'Novo jogador'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.players.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:input wire:model="nome" label="Nome" required autofocus />

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:select wire:model="team_id" label="Equipa" required>
                    @foreach ($teams as $t)
                        <flux:select.option value="{{ $t->id }}">{{ $t->nome }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="papel" label="Papel">
                    @foreach ($papeis as $p)
                        <flux:select.option value="{{ $p }}">{{ ucfirst(str_replace('-', ' ', $p)) }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="numero" label="Número" placeholder="Ex.: 7" />
                <flux:input wire:model="posicao" label="Posição" placeholder="Ex.: Base, Poste" />
                <flux:input type="number" wire:model="ano_nascimento" label="Ano de nascimento" placeholder="Ex.: 2008" />
                <flux:input type="number" wire:model="ordem" label="Ordem" min="0" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <x-admin.image-field
                    model="foto"
                    label="Fotografia"
                    :file="$foto"
                    :current="$fotoAtual"
                    remove="removerFoto"
                    hint="JPG/PNG até 4MB." />
            </div>

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit" class="flex-1">Guardar</flux:button>
                <flux:button variant="ghost" :href="route('admin.players.index')" wire:navigate>Cancelar</flux:button>
            </div>
        </div>
    </form>
</div>
