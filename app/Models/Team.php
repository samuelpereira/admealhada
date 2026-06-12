<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $sport_id
 * @property string $nome
 * @property string $slug
 * @property string|null $escalao
 * @property string|null $genero
 * @property string|null $competicao
 * @property string|null $fpb_url
 * @property string|null $descricao
 * @property string|null $foto
 * @property int $ordem
 *
 * @use HasFactory<TeamFactory>
 */
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'nome',
        'slug',
        'escalao',
        'genero',
        'competicao',
        'fpb_url',
        'descricao',
        'foto',
        'ordem',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<Sport, $this> */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /** @return HasMany<Player, $this> */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class)->orderBy('ordem')->orderBy('numero');
    }

    /** @return HasMany<Player, $this> */
    public function jogadores(): HasMany
    {
        return $this->players()->where('papel', 'jogador');
    }

    /** @return HasMany<Player, $this> */
    public function staff(): HasMany
    {
        return $this->hasMany(Player::class)->where('papel', '!=', 'jogador')->orderBy('ordem');
    }

    /** @return HasMany<Game, $this> */
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

    /** @return HasMany<Album, $this> */
    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function generoLabel(): string
    {
        return match ($this->genero) {
            'M' => 'Masculino',
            'F' => 'Feminino',
            default => 'Misto',
        };
    }
}
