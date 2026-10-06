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
 * @property bool $ativo
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
        'ativo',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'ativo' => true,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nome' => EncryptedString::class,
            'data_nascimento' => EncryptedDate::class,
            'ativo' => 'boolean',
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
    public static function countPeopleInCurrentSeason(?string $papel = null, bool $onlyVisible = false): int
    {
        $players = static::query()
            ->onCurrentSeasonTeam()
            ->when($papel !== null, fn (Builder $query): Builder => $query->where('papel', $papel))
            ->when($onlyVisible, fn (Builder $query): Builder => $query->where('ativo', true));

        $identified = (clone $players)->whereNotNull('fpb_id')->distinct()->count('fpb_id');
        $manual = (clone $players)->whereNull('fpb_id')->count();

        return $identified + $manual;
    }

    /**
     * The FPB page has no birth date. Reuse one already stored for this athlete.
     */
    public function inheritBirthDate(): void
    {
        if ($this->fpb_id === null || $this->data_nascimento !== null) {
            return;
        }

        $source = static::query()
            ->where('fpb_id', $this->fpb_id)
            ->when($this->exists, fn (Builder $query) => $query->whereKeyNot($this->id))
            ->whereNotNull('data_nascimento')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        $birthDate = $source?->data_nascimento;

        if ($birthDate === null) {
            return;
        }

        $this->data_nascimento = $birthDate->copy();
    }

    /**
     * A birth date belongs to the athlete, so every roster row with this fpb_id stays in step.
     */
    public function shareBirthDate(): void
    {
        if ($this->fpb_id === null) {
            return;
        }

        $date = $this->data_nascimento?->toDateString();

        static::query()
            ->where('fpb_id', $this->fpb_id)
            ->whereKeyNot($this->id)
            ->get()
            ->each(function (Player $player) use ($date): void {
                if ($player->data_nascimento?->toDateString() === $date) {
                    return;
                }

                $player->data_nascimento = $date === null ? null : Carbon::parse($date);
                $player->save();
            });
    }
}
