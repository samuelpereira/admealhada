<?php

namespace App\Models;

use App\Models\Concerns\FlushesSitemapCache;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * @property int $id
 * @property int $season_id
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
    use FlushesSitemapCache, HasFactory;

    protected $fillable = [
        'season_id',
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

    public function resolveRouteBinding(mixed $value, $field = null): ?Model
    {
        $field ??= $this->getRouteKeyName();
        $query = $this->newQuery()->where($field, $value);

        if ($field === $this->getKeyName()) {
            return $query->first();
        }

        $season = request()->route('season');

        if ($season instanceof Season) {
            return $query->where('season_id', $season->getKey())->first();
        }

        if (is_string($season) && $season !== '') {
            return $query->whereHas(
                'season',
                fn (Builder $seasonQuery) => $seasonQuery->where('slug', $season),
            )->first();
        }

        return $query->currentSeason()->first();
    }

    public function publicShowUrl(): string
    {
        $this->loadMissing('season');

        if ($this->season === null || $this->season->is_current) {
            return route('equipas.show', $this);
        }

        return route('equipas.show.season', [
            'season' => $this->season,
            'team' => $this,
        ]);
    }

    public function publicIndexUrl(): string
    {
        $this->loadMissing('season');

        if ($this->season === null || $this->season->is_current) {
            return route('equipas');
        }

        return route('equipas.season', $this->season);
    }

    /** @return BelongsTo<Season, $this> */
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    /** @return BelongsTo<Sport, $this> */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * @param  Builder<Team>  $query
     * @return Builder<Team>
     */
    public function scopeCurrentSeason(Builder $query): Builder
    {
        return $query->where('season_id', Season::current()?->id);
    }

    /**
     * @param  Builder<Team>  $query
     * @return Builder<Team>
     */
    public function scopeForAdminLists(Builder $query): Builder
    {
        return $query->with('season')->orderBy('ordem')->orderBy('nome');
    }

    public function adminLabel(): string
    {
        $this->loadMissing('season');

        if ($this->season === null || $this->season->is_current) {
            return $this->nome;
        }

        return $this->nome.' ('.$this->season->name.')';
    }

    public function duplicateIntoNextSeason(): self
    {
        $this->loadMissing('season');

        if ($this->season === null) {
            throw new RuntimeException('A equipa não tem época.');
        }

        $next = $this->season->next();

        if ($next->teams()->where('slug', $this->slug)->exists()) {
            throw new RuntimeException('Já existe uma equipa com este slug na época '.$next->name.'.');
        }

        $copy = $this->replicate(['fpb_url']);
        $copy->season_id = $next->id;
        $copy->fpb_url = null;
        $copy->foto = $this->duplicateFoto();
        $copy->unsetRelation('season');
        $copy->setRelation('season', $next);
        $copy->save();

        return $copy;
    }

    protected function duplicateFoto(): ?string
    {
        if ($this->foto === null || ! Storage::disk('public')->exists($this->foto)) {
            return $this->foto;
        }

        $extension = pathinfo($this->foto, PATHINFO_EXTENSION);
        $directory = trim(dirname($this->foto), '.');
        $target = ($directory !== '' ? $directory.'/' : '').Str::uuid()->toString().($extension !== '' ? '.'.$extension : '');

        Storage::disk('public')->copy($this->foto, $target);

        return $target;
    }

    /** @return HasMany<Player, $this> */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class)->orderBy('ordem')->orderBy('numero');
    }

    /** @return HasMany<Player, $this> */
    public function jogadores(): HasMany
    {
        return $this->players()->where('papel', 'jogador')->where('ativo', true);
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

    /** @return BelongsToMany<Coach, $this> */
    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class)
            ->withTimestamps()
            ->orderBy('coach_team.created_at')
            ->orderBy('coach_team.id');
    }

    /** @return BelongsToMany<Sponsor, $this> */
    public function sponsors(): BelongsToMany
    {
        return $this->belongsToMany(Sponsor::class)
            ->where('sponsors.tipo', Sponsor::TIPO_PRINCIPAL)
            ->where('sponsors.ativo', true)
            ->whereNotNull('sponsors.logo')
            ->orderBy('sponsors.ordem')
            ->orderBy('sponsors.nome');
    }

    /** @return BelongsToMany<Sponsor, $this> */
    public function teamSponsors(): BelongsToMany
    {
        return $this->belongsToMany(Sponsor::class)
            ->where('sponsors.tipo', Sponsor::TIPO_PRINCIPAL)
            ->whereNotNull('sponsors.logo')
            ->orderBy('sponsors.ordem')
            ->orderBy('sponsors.nome');
    }

    /** @return BelongsToMany<Sponsor, $this> */
    public function allSponsors(): BelongsToMany
    {
        return $this->belongsToMany(Sponsor::class)->orderBy('sponsors.ordem');
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
