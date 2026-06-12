<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
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

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class)->orderBy('ordem')->orderBy('numero');
    }

    public function jogadores(): HasMany
    {
        return $this->players()->where('papel', 'jogador');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Player::class)->where('papel', '!=', 'jogador')->orderBy('ordem');
    }

    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }

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
