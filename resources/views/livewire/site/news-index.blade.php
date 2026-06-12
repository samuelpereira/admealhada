<div>
    <x-site.page-header
        eyebrow="Atualidade"
        title="Notícias e novidades"
        subtitle="As últimas do clube, dos jogos e da formação." />

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        {{-- Filtros de categoria --}}
        <div class="mb-8 flex flex-wrap gap-2">
            <button type="button" wire:click="filtrar('')"
                @class([
                    'rounded-full px-4 py-1.5 text-sm font-semibold transition',
                    'bg-brand-700 text-white' => $categoria === '',
                    'bg-surface text-body ring-1 ring-line hover:bg-surface-2' => $categoria !== '',
                ])>
                Todas
            </button>
            @foreach ($categorias as $cat)
                <button type="button" wire:click="filtrar('{{ $cat }}')"
                    @class([
                        'rounded-full px-4 py-1.5 text-sm font-semibold transition',
                        'bg-brand-700 text-white' => $categoria === $cat,
                        'bg-surface text-body ring-1 ring-line hover:bg-surface-2' => $categoria !== $cat,
                    ])>
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        @if ($noticias->isEmpty())
            <p class="rounded-xl border border-dashed border-line bg-surface p-8 text-center text-muted">Não há notícias nesta categoria.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($noticias as $news)
                    <x-site.news-card :news="$news" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $noticias->links() }}
            </div>
        @endif
    </section>
</div>
