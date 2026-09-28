<?php

namespace App\Models;

use App\Casts\EncryptedDate;
use App\Casts\EncryptedString;
use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $team_id
 * @property string|null $fpb_id
 * @property string $nome
 * @property Carbon|null $data_nascimento
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
        'data_nascimento',
        'numero',
        'posicao',
        'ano_nascimento',
        'foto',
        'papel',
        'ordem',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nome' => EncryptedString::class,
            'data_nascimento' => EncryptedDate::class,
        ];
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @param  Builder<Player>  $query
     * @return Builder<Player>
     */
    public function scopeOnCurrentSeasonTeam(Builder $query): Builder
    {
        return $query->whereRelation('team', 'season_id', Season::current()?->id);
    }

    /**
     * People on current-season teams. The same fpb_id on several teams counts once.
     * Rows without an fpb_id stay separate, because they cannot be matched.
     */
    public static function countPeopleInCurrentSeason(?string $papel = null): int
    {
        $players = static::query()
            ->onCurrentSeasonTeam()
            ->when($papel !== null, fn (Builder $query): Builder => $query->where('papel', $papel));

        $identified = (clone $players)->whereNotNull('fpb_id')->distinct()->count('fpb_id');
        $manual = (clone $players)->whereNull('fpb_id')->count();

        return $identified + $manual;
    }
}
