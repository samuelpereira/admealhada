<div>
    <x-site.page-header :title="$album->titulo" :subtitle="$album->descricao">
        <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-brand-200">
            @if ($album->data)<span>{{ $album->data->translatedFormat('d \d\e F \d\e Y') }}</span>@endif
            @if ($album->team)<span>·</span><a href="{{ route('equipas.show', $album->team) }}" class="hover:text-white">{{ $album->team->nome }}</a>@endif
        </div>
        <a href="{{ route('galeria') }}" class="mt-6 inline-flex min-h-10 items-center gap-1 text-sm font-black text-brand-200 hover:text-white">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Voltar à galeria
        </a>
    </x-site.page-header>

    <section
        x-data="{
            open: false,
            current: 0,
            photos: @js($photosData),
            show(i) { this.current = i; this.open = true; document.body.style.overflow = 'hidden'; },
            close() { this.open = false; document.body.style.overflow = ''; },
            next() { this.current = (this.current + 1) % this.photos.length; },
            prev() { this.current = (this.current - 1 + this.photos.length) % this.photos.length; },
        }"
        @keydown.window.escape="close()"
        @keydown.window.arrow-right="if (open) next()"
        @keydown.window.arrow-left="if (open) prev()"
        class="site-shell py-12">

        @if ($album->photos->isEmpty())
            <p class="site-card border-dashed p-8 text-center text-muted">Este álbum ainda não tem fotos.</p>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($album->photos as $i => $photo)
                    <button type="button" wire:key="photo-{{ $photo->id }}" @click="show({{ $i }})" class="site-card group relative aspect-square">
                        <x-site.media :src="$photo->caminho" :seed="$photo->id" :icon="false" class="transition duration-300 group-hover:scale-105" />
                        <span class="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/20">
                            <svg class="h-8 w-8 text-white opacity-0 transition group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 21l-5.197-5.197" /></svg>
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- Lightbox --}}
            <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 p-4" @click.self="close()">
                <button type="button" @click="close()" class="absolute right-4 top-4 grid h-11 w-11 place-items-center rounded-md bg-white/10 text-white hover:bg-white/20" aria-label="Fechar">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                </button>
                <button type="button" @click="prev()" class="absolute left-4 grid h-11 w-11 place-items-center rounded-md bg-white/10 text-white hover:bg-white/20" aria-label="Anterior">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
                </button>
                <button type="button" @click="next()" class="absolute right-4 grid h-11 w-11 place-items-center rounded-md bg-white/10 text-white hover:bg-white/20" aria-label="Seguinte">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                </button>

                <figure class="max-h-[85vh] w-full max-w-4xl">
                    <template x-if="photos[current] && photos[current].src">
                        <img :src="photos[current].src" :alt="photos[current].legenda" class="mx-auto max-h-[80vh] rounded-lg object-contain">
                    </template>
                    <template x-if="photos[current] && ! photos[current].src">
                        <div class="mx-auto flex aspect-video w-full items-center justify-center rounded-lg bg-gradient-to-br" :class="photos[current].gradient">
                            <svg class="h-16 w-16 text-white/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3v18M5.6 5.6c3 2.5 3 10.3 0 12.8M18.4 5.6c-3 2.5-3 10.3 0 12.8" /></svg>
                        </div>
                    </template>
                    <figcaption class="mt-4 text-center text-sm text-white/80" x-text="photos[current] ? photos[current].legenda : ''"></figcaption>
                </figure>
            </div>
        @endif
    </section>
</div>
