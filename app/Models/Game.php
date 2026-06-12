<?php

namespace App\Models;

use Database\Factories\GameFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property string|null $fpb_id
 * @property string $adversario
 * @property Carbon $data_hora
 * @property string|null $local
 * @property string $casa_fora
 * @property string|null $competicao
 * @property int|null $jornada
 * @property int|null $pontos_casa
 * @property int|null $pontos_fora
 * @property string $estado
 *
 * @use HasFactory<GameFactory>
 */
class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'fpb_id',
        'adversario',
        'data_hora',
        'local',
        'casa_fora',
        'competicao',
        'jornada',
        'pontos_casa',
        'pontos_fora',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
        ];
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @param  Builder<Game>  $query
     * @return Builder<Game>
     */
    public function scopeProximos(Builder $query): Builder
    {
        return $query->where('data_hora', '>=', now()->startOfDay())->orderBy('data_hora');
    }

    /**
     * @param  Builder<Game>  $query
     * @return Builder<Game>
     */
    public function scopeResultados(Builder $query): Builder
    {
        return $query->where('data_hora', '<', now()->startOfDay())->orderByDesc('data_hora');
    }

    /**
     * Época desportiva em curso (início em setembro).
     */
    public static function epocaAtual(): string
    {
        return self::epocaFromDate(now());
    }

    /**
     * Determina a época de um jogo (ex.: 2025/2026).
     */
    public static function epocaFromDate(DateTimeInterface $date): string
    {
        $date = Carbon::parse($date);
        $ano = $date->year;

        if ($date->month >= 9) {
            return $ano.'/'.($ano + 1);
        }

        return ($ano - 1).'/'.$ano;
    }

    /**
     * Épocas disponíveis para filtros.
     *
     * @return list<string>
     */
    public static function epocasDisponiveis(): array
    {
        $predefinidas = ['2025/2026', '2026/2027'];

        $dasBd = self::query()
            ->whereNotNull('data_hora')
            ->pluck('data_hora')
            ->map(fn ($data) => self::epocaFromDate(Carbon::parse($data)))
            ->unique();

        /** @var list<string> $epocas */
        $epocas = collect($predefinidas)
            ->merge($dasBd)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $epocas;
    }

    /**
     * @param  Builder<Game>  $query
     * @return Builder<Game>
     */
    public function scopeEpoca(Builder $query, string $epoca): Builder
    {
        if ($epoca === '' || ! preg_match('/^(\d{4})\/(\d{4})$/', $epoca, $partes)) {
            return $query;
        }

        $inicio = Carbon::create((int) $partes[1], 9, 1)->startOfDay();
        $fim = Carbon::create((int) $partes[2], 9, 1)->startOfDay();

        return $query->where('data_hora', '>=', $inicio)->where('data_hora', '<', $fim);
    }

    public function emCasa(): bool
    {
        return $this->casa_fora === 'casa';
    }

    public function vitoria(): ?bool
    {
        if ($this->estado !== 'terminado' || $this->pontos_casa === null || $this->pontos_fora === null) {
            return null;
        }

        $pontosAd = $this->emCasa() ? $this->pontos_casa : $this->pontos_fora;
        $pontosAdv = $this->emCasa() ? $this->pontos_fora : $this->pontos_casa;

        return $pontosAd > $pontosAdv;
    }

    /**
     * Etiqueta legível da competição para exibição no calendário.
     */
    public function competicaoLabel(): ?string
    {
        return self::competicaoLabelFrom($this->competicao);
    }

    public static function competicaoLabelFrom(?string $competicao): ?string
    {
        if (! $competicao) {
            return null;
        }

        if (str_contains($competicao, 'Inter-Associa')) {
            return 'Inter-Associações';
        }

        if (str_contains($competicao, 'Campeonato Distrital')) {
            return 'Campeonato Distrital';
        }

        if (str_contains($competicao, 'Torneio Distrital')) {
            return 'Torneio Distrital';
        }

        return $competicao;
    }

    /**
     * Chave estável para filtros de competição.
     */
    public static function competicaoFilterKey(?string $competicao): ?string
    {
        if (! $competicao) {
            return null;
        }

        if (str_contains($competicao, 'Inter-Associa')) {
            return 'inter-associacoes';
        }

        if (str_contains($competicao, 'Campeonato Distrital')) {
            return 'campeonato-distrital';
        }

        if (str_contains($competicao, 'Torneio Distrital')) {
            return 'torneio-distrital';
        }

        return 'comp-'.md5($competicao);
    }

    /**
     * @param  Builder<Game>  $query
     * @return Builder<Game>
     */
    public function scopeCompeticaoFilter(Builder $query, string $filter): Builder
    {
        if ($filter === '') {
            return $query;
        }

        return match ($filter) {
            'inter-associacoes' => $query->where('competicao', 'like', '%Inter-Associa%'),
            'campeonato-distrital' => $query->where('competicao', 'like', '%Campeonato Distrital%'),
            'torneio-distrital' => $query->where('competicao', 'like', '%Torneio Distrital%'),
            default => str_starts_with($filter, 'comp-')
                ? $query->whereRaw('MD5(competicao) = ?', [substr($filter, 5)])
                : $query,
        };
    }
}
