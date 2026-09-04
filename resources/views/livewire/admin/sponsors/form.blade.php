<div>
    <x-admin.header :title="$sponsor ? 'Editar patrocinador' : 'Novo patrocinador'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.sponsors.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:input wire:model="nome" label="Nome" required autofocus />
                <flux:input wire:model="link" label="Link" type="url" placeholder="https://..." required
                    description="Abre num novo separador no site público." />

                <flux:select wire:model.live="tipo" label="Tipo" required>
                    @foreach ($tipos as $valor => $label)
                        <flux:select.option value="{{ $valor }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>

                @if ($tipo === 'principal')
                    <div>
                        <flux:label>Equipas</flux:label>
                        <p class="mb-3 text-sm text-zinc-500">Seleciona as equipas associadas a este patrocinador principal.</p>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($equipas as $equipa)
                                <flux:checkbox wire:model="teamIds" value="{{ $equipa->id }}" :label="$equipa->adminLabel()" />
                            @endforeach
                        </div>
                        @error('teamIds')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <flux:input wire:model="ordem" label="Ordem" type="number" min="0"
                    description="Patrocinadores com ordem mais baixa aparecem primeiro." />
                <flux:checkbox wire:model="ativo" label="Ativo no site" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-5 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <x-admin.image-field
                    model="logo"
                    label="Logo"
                    :file="$logo"
                    :current="$logoAtual"
                    remove="removerLogo"
                    hint="PNG/JPG/SVG até 4MB. Fundo transparente recomendado." />
            </div>

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit" class="flex-1">Guardar</flux:button>
                <flux:button variant="ghost" :href="route('admin.sponsors.index')" wire:navigate>Cancelar</flux:button>
            </div>
        </div>
    </form>
</div>
