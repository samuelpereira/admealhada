<?php

namespace App\Models;

use Database\Factories\SportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 * @property string $slug
 * @property string|null $descricao
 * @property bool $ativo
 *
 * @use HasFactory<SportFactory>
 */
class Sport extends Model
{
    /** @use HasFactory<SportFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
