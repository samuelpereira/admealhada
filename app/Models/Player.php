<?php

namespace App\Models;

use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $team_id
 * @property string|null $fpb_id
 * @property string $nome
 * @property int|null $numero
 * @property string|null $posicao
 * @property int|null $ano_nascimento
 * @property string|null $foto
 * @property string $papel
 * @property int $ordem
 *
 * @use HasFactory<PlayerFactory>
 */
class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    protected $fillable = [
        'team_id',
        'fpb_id',
        'nome',
        'numero',
        'posicao',
        'ano_nascimento',
        'foto',
        'papel',
        'ordem',
    ];

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
