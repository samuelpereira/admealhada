<div>
    <x-site.page-header
        eyebrow="Fala connosco"
        title="Contactos"
        subtitle="Tens dúvidas, queres inscrever-te ou propor uma parceria? Estamos aqui para ajudar." />

    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-2">
            {{-- Info --}}
            <div>
                <h2 class="text-2xl font-extrabold text-ink">Informações de contacto</h2>
                <p class="mt-2 text-body">Visita-nos no pavilhão ou envia-nos uma mensagem.</p>

                <dl class="mt-8 space-y-6">
                    @if (filled(config('site.contact.address')))
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            </div>
                            <div>
                                <dt class="text-sm font-bold text-ink">Morada</dt>
                                <dd class="text-sm text-body">{!! nl2br(e(config('site.contact.address'))) !!}</dd>
                            </div>
                        </div>
                    @endif
                    @if (filled(config('site.contact.email')))
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                            </div>
                            <div>
                                <dt class="text-sm font-bold text-ink">Email</dt>
                                <dd class="text-sm text-body"><a href="mailto:{{ config('site.contact.email') }}" class="text-brand-700 hover:underline dark:text-brand-300">{{ config('site.contact.email') }}</a></dd>
                            </div>
                        </div>
                    @endif
                    @if (filled(config('site.contact.phone')))
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-300">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            </div>
                            <div>
                                <dt class="text-sm font-bold text-ink">Telefone</dt>
                                <dd class="text-sm text-body"><a href="tel:{{ preg_replace('/\s+/', '', config('site.contact.phone')) }}" class="text-brand-700 hover:underline dark:text-brand-300">{{ config('site.contact.phone') }}</a></dd>
                            </div>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Formulário --}}
            <div class="rounded-2xl border border-line bg-surface p-6 shadow-sm sm:p-8">
                @if ($enviado)
                    <div class="mb-6 flex items-start gap-3 rounded-lg bg-green-50 p-4 text-sm text-green-800 ring-1 ring-green-200 dark:bg-green-500/10 dark:text-green-300 dark:ring-green-500/30">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        <span>Mensagem enviada com sucesso! Entraremos em contacto brevemente.</span>
                    </div>
                @endif

                <form wire:submit="submit" class="space-y-5">
                    <div>
                        <label for="nome" class="mb-1.5 block text-sm font-semibold text-body">Nome</label>
                        <input type="text" id="nome" wire:model="nome" class="w-full rounded-lg border-line bg-surface text-ink text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="O teu nome">
                        @error('nome') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-semibold text-body">Email</label>
                        <input type="email" id="email" wire:model="email" class="w-full rounded-lg border-line bg-surface text-ink text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="email@exemplo.pt">
                        @error('email') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="assunto" class="mb-1.5 block text-sm font-semibold text-body">Assunto</label>
                        <input type="text" id="assunto" wire:model="assunto" class="w-full rounded-lg border-line bg-surface text-ink text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Assunto da mensagem">
                        @error('assunto') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="mensagem" class="mb-1.5 block text-sm font-semibold text-body">Mensagem</label>
                        <textarea id="mensagem" wire:model="mensagem" rows="5" class="w-full rounded-lg border-line bg-surface text-ink text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Escreve a tua mensagem..."></textarea>
                        @error('mensagem') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-brand-700 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-brand-800 disabled:opacity-60" wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit">Enviar mensagem</span>
                        <span wire:loading wire:target="submit">A enviar...</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
