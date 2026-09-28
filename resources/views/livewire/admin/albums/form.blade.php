<div>
    <x-admin.header :title="$album ? 'Editar galeria' : 'Nova galeria'">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.albums.index')" wire:navigate>Voltar</flux:button>
    </x-admin.header>

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm lg:col-span-2 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:input wire:model="titulo" label="Título" required autofocus />

            <div class="grid gap-6 sm:grid-cols-2">
                <flux:select wire:model="team_id" label="Equipa (opcional)">
                    <flux:select.option value="">— Sem equipa —</flux:select.option>
                    @foreach ($teams as $t)
                        <flux:select.option value="{{ $t->id }}">{{ $t->adminLabel() }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:input type="date" wire:model="data" label="Data" />
            </div>

            <flux:textarea wire:model="descricao" label="Descrição" rows="3" description="Uma linha nova faz quebra de linha. **texto** fica a negrito." />
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <x-admin.image-field
                    model="capa"
                    label="Capa"
                    :file="$capa"
                    :current="$capaAtual"
                    remove="removerCapa"
                    hint="Ou defina a partir de uma foto abaixo." />
            </div>

            <div class="flex items-center gap-3">
                <flux:button variant="primary" type="submit" class="flex-1">Guardar</flux:button>
                <flux:button variant="ghost" :href="route('admin.albums.index')" wire:navigate>Cancelar</flux:button>
            </div>
        </div>
    </form>

    {{-- Gestão de fotos --}}
    <div class="mt-8 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="lg">Fotografias</flux:heading>

        @if (! $album)
            <flux:text class="mt-2 text-zinc-500">Guarde a galeria para poder adicionar fotografias.</flux:text>
        @else
            <div class="mt-4 space-y-3">
                <input
                    type="file"
                    wire:model="novasFotos"
                    accept="image/*"
                    multiple
                    class="block w-full text-sm text-zinc-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700 dark:text-zinc-300"
                >
                <div wire:loading wire:target="novasFotos">
                    <flux:text size="sm" class="text-brand-600">A carregar…</flux:text>
                </div>
                @error('novasFotos.*')
                    <flux:text size="sm" class="text-red-600">{{ $message }}</flux:text>
                @enderror

                @if (count($novasFotos))
                    <flux:button size="sm" variant="primary" icon="arrow-up-tray" wire:click="guardarFotos">
                        Adicionar {{ count($novasFotos) }} foto(s)
                    </flux:button>
                @endif
            </div>

            @if ($fotos->isEmpty())
                <flux:text class="mt-6 text-zinc-500">Ainda não há fotografias nesta galeria.</flux:text>
            @else
                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($fotos as $foto)
                        <div wire:key="foto-{{ $foto->id }}" class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <div class="aspect-square bg-zinc-100 dark:bg-zinc-800">
                                @if ($foto->caminho)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($foto->miniatura ?: $foto->caminho) }}" class="h-full w-full object-cover" alt="">
                                @endif
                            </div>
                            <div class="space-y-2 p-2">
                                <flux:input size="sm" wire:model="legendas.{{ $foto->id }}"
                                    wire:change="updateLegenda({{ $foto->id }})" placeholder="Legenda" />
                                <div class="flex items-center justify-between">
                                    <flux:button size="xs" variant="subtle" icon="star" wire:click="definirCapa({{ $foto->id }})"
                                        :disabled="$capaAtual === $foto->caminho">
                                        {{ $capaAtual === $foto->caminho ? 'Capa' : 'Definir capa' }}
                                    </flux:button>
                                    <flux:button size="xs" variant="subtle" icon="trash"
                                        wire:click="deletePhoto({{ $foto->id }})"
                                        wire:confirm="Remover esta foto?" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</div>
