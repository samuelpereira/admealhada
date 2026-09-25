<div>
    <x-admin.header :title="$coach ? 'Editar treinador' : 'Novo treinador'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.coaches.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:input wire:model="nome" label="Nome" required autofocus />
                <flux:input wire:model="dataNascimento" label="Data de nascimento" type="date" />
                <flux:input wire:model="nacionalidade" label="Nacionalidade" />
                <flux:input wire:model="grau" label="Grau" placeholder="Grau I" />

                <div>
                    <flux:label>Equipas</flux:label>
                    <p class="mb-3 text-sm text-zinc-500">O mesmo treinador pode estar em várias equipas.</p>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($equipas as $equipa)
                            <flux:checkbox wire:model="teamIds" value="{{ $equipa->id }}" :label="$equipa->adminLabel()" />
                        @endforeach
                    </div>
                    @error('teamIds')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-5 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <x-admin.image-field
                    model="foto"
                    label="Foto"
                    :file="$foto"
                    :current="$fotoAtual"
                    remove="removerFoto"
                    hint="PNG ou JPG até 4MB." />
            </div>

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit" class="flex-1">Guardar</flux:button>
                <flux:button variant="ghost" :href="route('admin.coaches.index')" wire:navigate>Cancelar</flux:button>
            </div>
        </div>
    </form>
</div>
