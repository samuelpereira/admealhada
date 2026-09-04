<div>
    <x-site.page-header
        eyebrow="Multimédia"
        title="Galeria de fotos"
        subtitle="Revive os melhores momentos, dentro e fora de campo." />

    <section class="site-shell py-12">
        @if ($albuns->isEmpty())
            <p class="site-card border-dashed p-8 text-center text-muted">Ainda não há álbuns publicados.</p>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($albuns as $album)
                    <a href="{{ route('galeria.show', $album) }}" class="site-card group flex flex-col hover:-translate-y-1">
                        <div class="relative aspect-[4/3] overflow-hidden">
                            <x-site.media :src="$album->capa" :seed="$album->id" :icon="false" class="transition duration-300 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-950/60 to-transparent"></div>
                            <span class="absolute bottom-3 right-3 rounded-md bg-rim-500 px-3 py-1 text-xs font-black text-white">{{ $album->photos_count }} fotos</span>
                        </div>
                        <div class="flex flex-1 flex-col p-5">
                            <h2 class="text-lg font-black text-ink group-hover:text-brand-600 dark:group-hover:text-brand-300">{{ $album->titulo }}</h2>
                            <div class="mt-1 flex items-center gap-2 text-xs text-muted">
                                @if ($album->data)<span>{{ $album->data->translatedFormat('d M Y') }}</span>@endif
                                @if ($album->team)<span>·</span><span>{{ $album->team->nome }}</span>@endif
                            </div>
                            <p class="mt-2 line-clamp-2 text-sm text-body">{{ $album->descricao }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
