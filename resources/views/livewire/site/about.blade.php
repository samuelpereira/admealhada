<div>
    <x-site.page-header
        eyebrow="Quem somos"
        title="O Clube"
        subtitle="A Academia Desportiva da Mealhada é mais do que basquetebol: é uma família que cresce a cada época." />

    {{-- Estatísticas --}}
    <section class="border-b border-line bg-surface">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-10 sm:px-6 lg:grid-cols-4 lg:px-8">
            <div class="text-center">
                <p class="text-4xl font-black text-brand-700 dark:text-brand-300">{{ $totalEquipas }}</p>
                <p class="mt-1 text-sm font-semibold uppercase tracking-wide text-muted">Equipas</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-black text-brand-700 dark:text-brand-300">{{ $totalAtletas }}+</p>
                <p class="mt-1 text-sm font-semibold uppercase tracking-wide text-muted">Atletas</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-black text-brand-700 dark:text-brand-300">5</p>
                <p class="mt-1 text-sm font-semibold uppercase tracking-wide text-muted">Escalões</p>
            </div>
            <div class="text-center">
                <p class="text-4xl font-black text-brand-700 dark:text-brand-300">CN2</p>
                <p class="mt-1 text-sm font-semibold uppercase tracking-wide text-muted">Competição sénior</p>
            </div>
        </div>
    </section>

    {{-- Missão --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <h2 class="flex items-center gap-2 text-2xl font-extrabold text-ink sm:text-3xl">
                    <span class="h-7 w-1.5 rounded-full bg-brand-600"></span>
                    A nossa missão
                </h2>
                <div class="mt-5 space-y-4 text-body">
                    <p>A AD Mealhada dedica-se à promoção do basquetebol no concelho da Mealhada, apostando na formação de jovens atletas desde os primeiros passos no minibasquete até à competição sénior.</p>
                    <p>Acreditamos no desporto como ferramenta de educação, promovendo valores como o espírito de equipa, a disciplina, o respeito e a superação, dentro e fora do campo.</p>
                    <p>A equipa sénior masculina compete no Campeonato Nacional da 2.ª Divisão (CN2), representando com orgulho a cidade e os seus adeptos.</p>
                </div>
            </div>
            <div class="relative aspect-[4/3] overflow-hidden rounded-2xl shadow-lg">
                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-brand-700 to-brand-950">
                    <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="AD Mealhada" class="h-48 w-48 object-contain opacity-90">
                </div>
            </div>
        </div>
    </section>

    {{-- Valores --}}
    <section class="bg-surface py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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
                    <div class="rounded-xl border border-line bg-surface-2 p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-600 text-white">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-ink">{{ $titulo }}</h3>
                        <p class="mt-1 text-sm text-body">{{ $texto }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Modalidades --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <x-site.section-heading title="Modalidades" subtitle="Por agora dedicamo-nos ao basquetebol, mas o clube está aberto ao crescimento." />
        <div class="rounded-2xl border border-brand-200 bg-brand-50 p-8 dark:border-brand-500/30 dark:bg-brand-500/10">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-brand-600 text-white">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9" /><path d="M3 12h18M12 3v18M5.6 5.6c3 2.5 3 10.3 0 12.8M18.4 5.6c-3 2.5-3 10.3 0 12.8" /></svg>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-ink">Basquetebol</h3>
                    <p class="text-sm text-body">Escalões de formação (Sub-10 a Sub-16, masculinos e femininos) e equipa sénior masculina na CN2.</p>
                </div>
            </div>
        </div>
    </section>
</div>
