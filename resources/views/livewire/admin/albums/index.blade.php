<div>
    <x-admin.header title="Galerias" subtitle="Álbuns de fotografias">
        <flux:button variant="primary" icon="plus" :href="route('admin.albums.create')" wire:navigate>Nova galeria</flux:button>
    </x-admin.header>

    @if ($albums->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-12 text-center text-zinc-500 dark:border-zinc-700">
            Ainda não há galerias.
        </div>
    @else
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($albums as $album)
                <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="aspect-video bg-zinc-100 dark:bg-zinc-800">
                        @if ($album->capa)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($album->capa_miniatura ?: $album->capa) }}" class="h-full w-full object-cover" alt="">
                        @else
                            <div class="flex h-full items-center justify-center">
                                <flux:icon.photo class="h-10 w-10 text-zinc-300" />
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $album->titulo }}</div>
                        <div class="mt-0.5 text-xs text-zinc-500">
                            {{ $album->photos_count }} foto(s)
                            @if ($album->data) · {{ $album->data->format('d/m/Y') }} @endif
                            @if ($album->team) · {{ $album->team->nome }} @endif
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.albums.edit', $album)" wire:navigate>Gerir</flux:button>
                            <flux:button size="xs" variant="subtle" icon="trash"
                                wire:click="delete('{{ $album->slug }}')"
                                wire:confirm="Eliminar a galeria '{{ $album->titulo }}' e todas as fotos?">Eliminar</flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
