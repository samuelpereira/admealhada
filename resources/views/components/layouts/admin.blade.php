<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-1 py-2" wire:navigate>
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="AD Mealhada" class="h-9 w-9 object-contain">
                    <div class="leading-tight">
                        <span class="block text-sm font-extrabold tracking-wide text-zinc-900 dark:text-white">AD MEALHADA</span>
                        <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-brand-600 dark:text-brand-300">Backoffice</span>
                    </div>
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Geral')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>
                        Dashboard
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')" wire:navigate>
                        Utilizadores
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Conteúdo')" class="grid">
                    <flux:sidebar.item icon="newspaper" :href="route('admin.news.index')" :current="request()->routeIs('admin.news.*')" wire:navigate>
                        Notícias
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="photo" :href="route('admin.albums.index')" :current="request()->routeIs('admin.albums.*')" wire:navigate>
                        Galerias
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-storefront" :href="route('admin.sponsors.index')" :current="request()->routeIs('admin.sponsors.*')" wire:navigate>
                        Patrocinadores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('admin.social-bodies.index')" :current="request()->routeIs('admin.social-bodies.*')" wire:navigate>
                        Órgãos sociais
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Competição')" class="grid">
                    <flux:sidebar.item icon="trophy" :href="route('admin.sports.index')" :current="request()->routeIs('admin.sports.*')" wire:navigate>
                        Modalidades
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar-date-range" :href="route('admin.seasons.index')" :current="request()->routeIs('admin.seasons.*')" wire:navigate>
                        Épocas
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" :href="route('admin.teams.index')" :current="request()->routeIs('admin.teams.*')" wire:navigate>
                        Equipas
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="identification" :href="route('admin.players.index')" :current="request()->routeIs('admin.players.*')" wire:navigate>
                        Jogadores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="academic-cap" :href="route('admin.coaches.index')" :current="request()->routeIs('admin.coaches.*')" wire:navigate>
                        Treinadores
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="calendar-days" :href="route('admin.games.index')" :current="request()->routeIs('admin.games.*')" wire:navigate>
                        Jogos
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="globe-alt" :href="route('home')" target="_blank">
                    Ver o site
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile :name="auth()->user()->name" :initials="auth()->user()->initials()" icon-trailing="chevrons-up-down" />

                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Definições</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            Terminar sessão
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

                <flux:menu>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Definições</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer">
                            Terminar sessão
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main>
            {{ $slot }}
        </flux:main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
