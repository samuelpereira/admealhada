<div>
    <x-site.page-header
        eyebrow="Quem somos"
        title="O Clube"
        subtitle="A Academia Desportiva da Mealhada é mais do que basquetebol: é uma família que cresce a cada época." />

    {{-- Estatísticas --}}
    <section class="border-b border-line bg-surface/80">
        <div class="site-shell grid grid-cols-2 gap-6 py-10 lg:grid-cols-4">
            <div class="text-center">
                <p class="text-4xl font-black text-rim-600 dark:text-rim-300">{{ $totalEquipas }}</p>
                <p class="mt-1 text-sm font-black uppercase text-muted">Equipas</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-black text-rim-600 dark:text-rim-300">{{ $totalJogadores }}</p>
                <p class="mt-1 text-sm font-black uppercase text-muted">Jogadores</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-black text-rim-600 dark:text-rim-300">{{ $totalPatrocinadores }}</p>
                <p class="mt-1 text-sm font-black uppercase text-muted">Patrocinadores</p>
            </div>
        </div>
    </section>

    {{-- Missão --}}
    <section class="site-shell py-14">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <h2 class="flex items-center gap-3 text-2xl font-black uppercase text-ink sm:text-3xl">
                    <span class="h-1.5 w-8 rounded-full bg-rim-500"></span>
                    A nossa missão
                </h2>
                <div class="mt-5 space-y-4 text-body">
                    <p>A AD Mealhada dedica-se à promoção do basquetebol no concelho da Mealhada, apostando na formação de jovens atletas desde os primeiros passos no minibasquete até à competição sénior.</p>
                    <p>Acreditamos no desporto como ferramenta de educação, promovendo valores como o espírito de equipa, a disciplina, o respeito e a superação, dentro e fora do campo.</p>
                    <p>A equipa sénior masculina compete no Campeonato Nacional da 2.ª Divisão (CN2), representando com orgulho a cidade e os seus adeptos.</p>
                </div>
            </div>
            <div class="site-card relative aspect-[4/3]">
                <div class="court-lines absolute inset-0 flex items-center justify-center bg-gradient-to-br from-brand-700 to-brand-950">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="AD Mealhada" class="h-48 w-48 object-contain opacity-90">
                </div>
            </div>
        </div>
    </section>

    {{-- Valores --}}
    <section class="bg-surface/80 py-14">
        <div class="site-shell">
            <x-site.section-heading title="Os nossos valores" />
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['Formação', 'O desenvolvimento dos jovens atletas é o coração do clube.'],
                    ['Espírito de equipa', 'Juntos somos mais fortes, dentro e fora do campo.'],
                    ['Respeito', 'Pelos colegas, adversários, árbitros e adeptos.'],
                    ['Disciplina', 'Trabalho e dedicação como base do sucesso.'],
                    ['Comunidade', 'Um clube ao serviço da cidade da Mealhada.'],
                    ['Ambição', 'Crescer todos os dias e levar o clube mais longe.'],
                ] as [$titulo, $texto])
                    <div class="site-card p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-md bg-rim-500 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-ink">{{ $titulo }}</h3>
                        <p class="mt-1 text-sm text-body">{{ $texto }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Órgãos sociais --}}
    @if ($orgaosSociais->isNotEmpty())
        <section class="site-shell py-14">
                <x-site.section-heading title="Órgãos Sociais" subtitle="A estrutura de governação do clube." />

                <nav class="mb-10 flex flex-wrap gap-3">
                    @foreach ($categorias as $slug => $label)
                        @if ($orgaosSociais->has($slug))
                            <a href="#{{ $slug }}" class="min-h-10 rounded-md border border-line bg-surface-2 px-4 py-2 text-sm font-black text-ink transition hover:border-brand-400 hover:text-brand-700 dark:hover:text-brand-300">
                                {{ $label }}
                            </a>
                        @endif
                    @endforeach
                </nav>

                <div class="grid gap-10 lg:grid-cols-3">
                    @foreach ($categorias as $slug => $label)
                        @if ($orgaosSociais->has($slug))
                            <div id="{{ $slug }}" class="scroll-mt-24">
                                <h3 class="flex items-center gap-2 text-lg font-black uppercase text-ink">
                                    <span class="h-1.5 w-6 rounded-full bg-rim-500"></span>
                                    {{ $label }}
                                </h3>
                                <ul class="site-card mt-4 divide-y divide-line bg-surface-2">
                                    @foreach ($orgaosSociais[$slug] as $membro)
                                        <li class="flex items-baseline justify-between gap-4 px-5 py-4">
                                            <span class="text-sm font-semibold text-brand-700 dark:text-brand-300">{{ $membro->cargo }}</span>
                                            <span class="text-sm text-ink">{{ $membro->nome }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
        </section>
    @endif

    {{-- Modalidades --}}
    <section class="bg-surface/80 py-14">
        <div class="site-shell">
            <x-site.section-heading title="Modalidades" subtitle="As modalidades desportivas do clube." />
            <div class="site-card p-8">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-md bg-rim-500 text-white">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3v18M5.6 5.6c3 2.5 3 10.3 0 12.8M18.4 5.6c-3 2.5-3 10.3 0 12.8" /></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-ink">Basquetebol</h3>
                        <p class="text-sm text-body">Modalidade principal da Academia Desportiva da Mealhada, com escalões de formação e equipa sénior.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
