<div>
    <x-site.page-header
        eyebrow="Atualidade"
        title="Notícias e novidades"
        subtitle="As novidades, os resultados e as histórias que fazem o nosso clube." />

    <section class="site-shell py-12">
        {{-- Filtros de categoria --}}
        <div class="scroll-tabs mb-8">
            <button type="button" wire:click="filtrar('')"
                @class([
                    'min-h-10 shrink-0 rounded-md px-4 py-2 text-sm font-black uppercase transition',
                    'bg-brand-700 text-white shadow-sm' => $categoria === '',
                    'bg-surface text-body ring-1 ring-line hover:bg-surface-2' => $categoria !== '',
                ])>
                Todas
            </button>
            @foreach ($categorias as $cat)
                <button type="button" wire:click="filtrar('{{ $cat }}')"
                    @class([
                        'min-h-10 shrink-0 rounded-md px-4 py-2 text-sm font-black uppercase transition',
                        'bg-brand-700 text-white shadow-sm' => $categoria === $cat,
                        'bg-surface text-body ring-1 ring-line hover:bg-surface-2' => $categoria !== $cat,
                    ])>
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        @if ($noticias->isEmpty())
            <p class="site-card border-dashed p-8 text-center text-muted">Não há notícias nesta categoria.</p>
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
