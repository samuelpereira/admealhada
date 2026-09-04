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

<div class="site-card flex min-h-48 flex-col p-4 hover:-translate-y-1">
    <div class="mb-3 flex items-center justify-between gap-2 text-xs">
        <span class="font-black uppercase text-brand-700 dark:text-brand-200">
            {{ $game->data_hora->translatedFormat('d M Y · H\hi') }}
        </span>
        @if ($terminado)
            @if ($vitoria === true)
                <span class="rounded-md bg-green-100 px-2 py-1 font-black text-green-700 dark:bg-green-500/15 dark:text-green-400">Vitória</span>
            @elseif ($vitoria === false)
                <span class="rounded-md bg-red-100 px-2 py-1 font-black text-red-700 dark:bg-red-500/15 dark:text-red-400">Derrota</span>
            @endif
        @else
            <span class="rounded-md bg-rim-100 px-2 py-1 font-black text-rim-800 dark:bg-rim-500/20 dark:text-rim-200">{{ $emCasa ? 'Casa' : 'Fora' }}</span>
        @endif
    </div>

    <div class="space-y-2 rounded-md bg-surface-2 p-3">
        <div class="flex items-center justify-between gap-3">
            <span @class(['truncate text-sm uppercase', 'font-black text-ink' => $topIsAd, 'font-semibold text-muted' => ! $topIsAd])>{{ $topNome }}</span>
            <span @class(['min-w-9 text-right text-2xl font-black leading-none', 'text-rim-600 dark:text-rim-300' => $topIsAd, 'text-muted' => ! $topIsAd])>{{ $terminado ? $topPontos : '–' }}</span>
        </div>
        <div class="flex items-center justify-between gap-3">
            <span @class(['truncate text-sm uppercase', 'font-black text-ink' => $bottomIsAd, 'font-semibold text-muted' => ! $bottomIsAd])>{{ $bottomNome }}</span>
            <span @class(['min-w-9 text-right text-2xl font-black leading-none', 'text-rim-600 dark:text-rim-300' => $bottomIsAd, 'text-muted' => ! $bottomIsAd])>{{ $terminado ? $bottomPontos : '–' }}</span>
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
