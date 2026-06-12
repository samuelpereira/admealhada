<div>
    <x-admin.header :title="$game ? 'Editar jogo' : 'Novo jogo'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.games.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="max-w-3xl space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid gap-6 sm:grid-cols-2">
            <flux:select wire:model="team_id" label="Equipa do clube" required>
                @foreach ($teams as $t)
                    <flux:select.option value="{{ $t->id }}">{{ $t->nome }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:input wire:model="adversario" label="Adversário" required />

            <flux:input type="datetime-local" wire:model="data_hora" label="Data e hora" required />

            <flux:select wire:model="casa_fora" label="Local do jogo">
                <flux:select.option value="casa">Em casa</flux:select.option>
                <flux:select.option value="fora">Fora</flux:select.option>
            </flux:select>

            <flux:input wire:model="local" label="Pavilhão / Local" />
            <flux:input wire:model="competicao" label="Competição" placeholder="Ex.: CN2" />
            <flux:input wire:model="jornada" label="Jornada" placeholder="Ex.: 5" />

            <flux:select wire:model.live="estado" label="Estado">
                @foreach ($estados as $e)
                    <flux:select.option value="{{ $e }}">{{ ucfirst($e) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if ($estado === 'terminado')
            <div class="grid gap-6 rounded-lg bg-zinc-50 p-4 sm:grid-cols-2 dark:bg-zinc-800/50">
                <flux:input type="number" wire:model="pontos_casa" label="Pontos casa" min="0" />
                <flux:input type="number" wire:model="pontos_fora" label="Pontos fora" min="0" />
            </div>
        @endif

        <div class="flex items-center gap-3 border-t border-zinc-100 pt-4 dark:border-zinc-800">
            <flux:button variant="primary" type="submit">Guardar</flux:button>
            <flux:button variant="ghost" :href="route('admin.games.index')" wire:navigate>Cancelar</flux:button>
        </div>
    </form>
</div>
