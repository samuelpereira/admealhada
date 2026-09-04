<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sponsor extends Model
{
    public const TIPO_PRINCIPAL = 'principal';

    public const TIPO_GERAL = 'geral';

    /** @var array<string, string> */
    public const TIPOS = [
        self::TIPO_PRINCIPAL => 'Principal',
        self::TIPO_GERAL => 'Geral',
    ];

    protected $fillable = [
        'nome',
        'logo',
        'link',
        'tipo',
        'ordem',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ordem' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    /** @return BelongsToMany<Team, $this> */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->orderBy('teams.ordem');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query
            ->where('ativo', true)
            ->orderBy('ordem')
            ->orderBy('nome');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeGerais(Builder $query): Builder
    {
        return $query->where('tipo', self::TIPO_GERAL);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePrincipais(Builder $query): Builder
    {
        return $query->where('tipo', self::TIPO_PRINCIPAL);
    }

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function isPrincipal(): bool
    {
        return $this->tipo === self::TIPO_PRINCIPAL;
    }
}
