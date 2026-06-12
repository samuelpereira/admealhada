<div>
    <x-admin.header title="Notícias" subtitle="Gerir publicações do clube">
        <flux:button variant="primary" icon="plus" :href="route('admin.news.create')" wire:navigate>Nova notícia</flux:button>
    </x-admin.header>

    <div class="mb-4 max-w-sm">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Pesquisar por título…" />
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase tracking-wide text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/50">
                <tr>
                    <th class="px-4 py-3">Título</th>
                    <th class="px-4 py-3">Categoria</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($news as $item)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-14 shrink-0 overflow-hidden rounded bg-zinc-100 dark:bg-zinc-800">
                                    @if ($item->capa)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->capa) }}" class="h-full w-full object-cover" alt="">
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $item->titulo }}</div>
                                    @if ($item->destaque)
                                        <flux:badge color="amber" size="sm">Destaque</flux:badge>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $item->categoria }}</td>
                        <td class="px-4 py-3">
                            @if ($item->publicado_em && $item->publicado_em->isPast())
                                <flux:badge color="green" size="sm">Publicada</flux:badge>
                                <div class="mt-1 text-xs text-zinc-500">{{ $item->publicado_em->format('d/m/Y H:i') }}</div>
                            @elseif ($item->publicado_em)
                                <flux:badge color="blue" size="sm">Agendada</flux:badge>
                                <div class="mt-1 text-xs text-zinc-500">{{ $item->publicado_em->format('d/m/Y H:i') }}</div>
                            @else
                                <flux:badge color="zinc" size="sm">Rascunho</flux:badge>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <flux:button size="xs" variant="subtle" icon="pencil" :href="route('admin.news.edit', $item)" wire:navigate>Editar</flux:button>
                                <flux:button size="xs" variant="subtle" icon="trash"
                                    wire:click="delete('{{ $item->slug }}')"
                                    wire:confirm="Eliminar a notícia '{{ $item->titulo }}'?">Eliminar</flux:button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-zinc-500">Sem notícias.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $news->links() }}
    </div>
</div>
