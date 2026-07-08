<!DOCTYPE html>
<html lang="pt" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <title>{{ filled($title ?? null) ? $title.' - '.config('app.name') : config('app.name').' - Basquetebol' }}</title>
    <meta name="description" content="{{ $description ?? 'Site oficial da Academia Desportiva da Mealhada - Clube de basquetebol com escalões de formação e equipa sénior na CN2.' }}" />

    <link rel="icon" href="{{ Vite::asset('resources/images/logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ Vite::asset('resources/images/logo.png') }}">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-body antialiased font-sans flex flex-col">
    @php
        $navLinks = [
            ['route' => 'clube', 'label' => 'Clube'],
            ['route' => 'equipas', 'label' => 'Equipas'],
            ['route' => 'patrocinadores', 'label' => 'Patrocinadores'],
            ['route' => 'calendario', 'label' => 'Calendário'],
            ['route' => 'noticias', 'label' => 'Notícias'],
            ['route' => 'galeria', 'label' => 'Galeria'],
            ['route' => 'contactos', 'label' => 'Contactos'],
        ];
    @endphp

    <header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-line bg-white/95 text-body shadow-sm backdrop-blur supports-[backdrop-filter]:bg-white/80 dark:bg-zinc-950/95 dark:supports-[backdrop-filter]:bg-zinc-950/85">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between lg:h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="AD Mealhada" class="h-12 w-12 object-contain lg:h-14 lg:w-14">
                    <div class="leading-tight">
                        <span class="block text-base font-extrabold tracking-wide text-ink lg:text-lg">AD MEALHADA</span>
                        <span class="block text-[11px] font-medium uppercase tracking-[0.2em] text-brand-600 dark:text-brand-300">Basquetebol</span>
                    </div>
                </a>

                <nav class="hidden items-center gap-1 lg:flex">
                    @foreach ($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                           @class([
                               'rounded-md px-3 py-2 text-sm font-semibold transition',
                               'bg-brand-600 text-white' => request()->routeIs($link['route']),
                               'text-body hover:bg-surface-2 hover:text-ink dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-white' => ! request()->routeIs($link['route']),
                           ])>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    <span class="mx-1 h-5 w-px bg-line"></span>
                    <x-site.theme-toggle />
                </nav>

                <div class="flex items-center gap-1 lg:hidden">
                    <x-site.theme-toggle />
                    <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-body hover:bg-surface-2 dark:text-zinc-300 dark:hover:bg-white/10" aria-label="Menu">
                        <svg x-show="! open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" /></svg>
                        <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" x-cloak x-transition class="border-t border-line lg:hidden">
            <nav class="space-y-1 px-4 py-3">
                @foreach ($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                       @class([
                           'block rounded-md px-3 py-2 text-base font-semibold',
                           'bg-brand-600 text-white' => request()->routeIs($link['route']),
                           'text-body hover:bg-surface-2 hover:text-ink dark:text-zinc-300 dark:hover:bg-white/10 dark:hover:text-white' => ! request()->routeIs($link['route']),
                       ])>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </header>

    <main class="flex-1">
        {{ $slot }}
    </main>

    @unless (request()->routeIs('patrocinadores'))
        <x-site.sponsors-carousel :sponsors="$patrocinadores" />
    @endunless

    <footer @class(['bg-brand-950 text-brand-100', 'mt-16' => request()->routeIs('patrocinadores')])>
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div>
                    <div class="flex items-center gap-3">
                        <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="AD Mealhada" class="h-14 w-14 object-contain">
                        <div class="leading-tight">
                            <span class="block text-lg font-extrabold text-white">AD MEALHADA</span>
                            <span class="block text-xs uppercase tracking-widest text-brand-300">Basquetebol</span>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-brand-200/80">
                        Academia Desportiva da Mealhada. Formação e competição no basquetebol, do minibasquete aos seniores.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white">Navegação</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach ($navLinks as $link)
                            <li><a href="{{ route($link['route']) }}" class="hover:text-white">{{ $link['label'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white">Contactos</h3>
                    <ul class="mt-4 space-y-2 text-sm text-brand-200/90">
                        @foreach (array_filter(array_map('trim', explode("\n", config('site.contact.address')))) as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                        @if (filled(config('site.contact.email')))
                            <li><a href="mailto:{{ config('site.contact.email') }}" class="hover:text-white">{{ config('site.contact.email') }}</a></li>
                        @endif
                        @if (filled(config('site.contact.phone')))
                            <li><a href="tel:{{ preg_replace('/\s+/', '', config('site.contact.phone')) }}" class="hover:text-white">{{ config('site.contact.phone') }}</a></li>
                        @endif
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-white">Redes sociais</h3>
                    <div class="mt-4 flex gap-3">
                        @if (filled(config('site.contact.facebook')))
                            <a href="{{ config('site.contact.facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12Z"/></svg>
                            </a>
                        @endif
                        @if (filled(config('site.contact.instagram')))
                            <a href="{{ config('site.contact.instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 hover:bg-white/20">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.336 3.608 1.311.975.975 1.249 2.242 1.311 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.062 1.366-.336 2.633-1.311 3.608-.975.975-2.242 1.249-3.608 1.311-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.062-2.633-.336-3.608-1.311-.975-.975-1.249-2.242-1.311-3.608C2.175 15.584 2.163 15.204 2.163 12s.012-3.584.07-4.85c.062-1.366.336-2.633 1.311-3.608.975-.975 2.242-1.249 3.608-1.311C8.416 2.175 8.796 2.163 12 2.163Zm0 1.802c-3.15 0-3.523.012-4.767.069-1.024.047-1.58.218-1.95.362-.49.19-.84.418-1.207.785-.367.367-.595.717-.785 1.207-.144.37-.315.926-.362 1.95-.057 1.244-.069 1.617-.069 4.767s.012 3.523.069 4.767c.047 1.024.218 1.58.362 1.95.19.49.418.84.785 1.207.367.367.717.595 1.207.785.37.144.926.315 1.95.362 1.244.057 1.617.069 4.767.069s3.523-.012 4.767-.069c1.024-.047 1.58-.218 1.95-.362.49-.19.84-.418 1.207-.785.367-.367.595-.717.785-1.207.144-.37.315-.926.362-1.95.057-1.244.069-1.617.069-4.767s-.012-3.523-.069-4.767c-.047-1.024-.218-1.58-.362-1.95-.19-.49-.418-.84-.785-1.207-.367-.367-.717-.595-1.207-.785-.37-.144-.926-.315-1.95-.362-1.244-.057-1.617-.069-4.767-.069Zm0 3.063A5.135 5.135 0 1 0 12 17.135 5.135 5.135 0 0 0 12 7.028Zm0 8.468A3.333 3.333 0 1 1 12 8.83a3.333 3.333 0 0 1 0 6.666Zm6.538-8.671a1.2 1.2 0 1 1-2.4 0 1.2 1.2 0 0 1 2.4 0Z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-10 border-t border-white/10 pt-6 text-center text-xs text-brand-300/70">
                &copy; {{ date('Y') }} {{ config('site.contact.name') }}. Todos os direitos reservados.
            </div>
        </div>
    </footer>
</body>
</html>
