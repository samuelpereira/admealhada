@props([
    'game',
    'showTeam' => true,
])

@php
    $emCasa = $game->emCasa();
    $adNome = 'AD Mealhada';
    $advNome = $game->adversario;

    // Equipa de cima/baixo conforme casa/fora
    $topNome = $emCasa ? $adNome : $advNome;
    $topPontos = $emCasa ? $game->pontos_casa : $game->pontos_fora;
    $topIsAd = $emCasa;

    $bottomNome = $emCasa ? $advNome : $adNome;
    $bottomPontos = $emCasa ? $game->pontos_fora : $game->pontos_casa;
    $bottomIsAd = ! $emCasa;

    $terminado = $game->estado === 'terminado';
    $vitoria = $game->vitoria();
@endphp

<div class="flex flex-col rounded-xl border border-line bg-surface p-4 shadow-sm transition hover:shadow-md">
    <div class="mb-3 flex items-center justify-between gap-2 text-xs">
        <span class="font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-300">
            {{ $game->data_hora->translatedFormat('d M Y · H\hi') }}
        </span>
        @if ($terminado)
            @if ($vitoria === true)
                <span class="rounded-full bg-green-100 px-2 py-0.5 font-bold text-green-700 dark:bg-green-500/15 dark:text-green-400">Vitória</span>
            @elseif ($vitoria === false)
                <span class="rounded-full bg-red-100 px-2 py-0.5 font-bold text-red-700 dark:bg-red-500/15 dark:text-red-400">Derrota</span>
            @endif
        @else
            <span class="rounded-full bg-brand-100 px-2 py-0.5 font-semibold text-brand-700 dark:bg-brand-500/20 dark:text-brand-200">{{ $emCasa ? 'Casa' : 'Fora' }}</span>
        @endif
    </div>

    <div class="space-y-2">
        <div class="flex items-center justify-between gap-3">
            <span @class(['truncate text-sm', 'font-bold text-ink' => $topIsAd, 'text-muted' => ! $topIsAd])>{{ $topNome }}</span>
            <span @class(['min-w-7 text-right text-lg font-extrabold', 'text-brand-600 dark:text-brand-300' => $topIsAd, 'text-muted' => ! $topIsAd])>{{ $terminado ? $topPontos : '–' }}</span>
        </div>
        <div class="flex items-center justify-between gap-3">
            <span @class(['truncate text-sm', 'font-bold text-ink' => $bottomIsAd, 'text-muted' => ! $bottomIsAd])>{{ $bottomNome }}</span>
            <span @class(['min-w-7 text-right text-lg font-extrabold', 'text-brand-600 dark:text-brand-300' => $bottomIsAd, 'text-muted' => ! $bottomIsAd])>{{ $terminado ? $bottomPontos : '–' }}</span>
        </div>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 border-t border-line pt-3 text-xs text-muted">
        @if ($showTeam)
            <a href="{{ route('equipas.show', $game->team) }}" class="font-semibold text-brand-600 hover:underline dark:text-brand-300">{{ $game->team->nome }}</a>
            <span>·</span>
        @endif
        @if ($game->competicaoLabel())
            <span class="font-medium text-body" title="{{ $game->competicao }}">{{ $game->competicaoLabel() }}</span>
            <span>·</span>
        @endif
        @if ($game->jornada)
            <span>Jornada {{ $game->jornada }}</span>
            <span>·</span>
        @endif
        <span class="truncate">{{ $game->local }}</span>
    </div>
</div>
