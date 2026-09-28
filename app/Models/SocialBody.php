<?php

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SocialBody extends Model
{
    /** @var array<string, string> */
    public const CATEGORIAS = [
        'direcao' => 'Direção',
        'assembleia_geral' => 'Assembleia Geral',
        'conselho_fiscal' => 'Conselho Fiscal',
    ];

    protected $fillable = [
        'categoria',
        'cargo',
        'nome',
        'ordem',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'nome' => EncryptedString::class,
            'ordem' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query
            ->where('ativo', true)
            ->orderBy('ordem');
    }

    public function categoriaLabel(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? $this->categoria;
    }
}
