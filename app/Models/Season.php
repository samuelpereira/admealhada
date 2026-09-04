<?php

namespace App\Models;

use App\Models\Concerns\FlushesSitemapCache;
use Database\Factories\SeasonFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property bool $is_current
 *
 * @use HasFactory<SeasonFactory>
 */
class Season extends Model
{
    /** @use HasFactory<SeasonFactory> */
    use FlushesSitemapCache, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'starts_at',
        'ends_at',
        'is_current',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function publicIndexUrl(): string
    {
        return $this->is_current
            ? route('equipas')
            : route('equipas.season', $this);
    }

    public function periodLabel(): string
    {
        return $this->starts_at->format('d/m/Y').' – '.$this->ends_at->format('d/m/Y');
    }

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function next(): self
    {
        $startYear = (int) $this->starts_at->year + 1;

        return self::ensureForName($startYear.'/'.($startYear + 1));
    }

    /**
     * Sporting season that contains the given date (starts in September).
     *
     * @return array{name: string, slug: string, starts_at: string, ends_at: string}
     */
    public static function attributesForDate(DateTimeInterface $date): array
    {
        $date = Carbon::parse($date);
        $startYear = $date->month >= 9 ? $date->year : $date->year - 1;

        return self::attributesForName($startYear.'/'.($startYear + 1));
    }

    /**
     * @return array{name: string, slug: string, starts_at: string, ends_at: string}
     */
    public static function attributesForName(string $name): array
    {
        if (! preg_match('/^(\d{4})\/(\d{4})$/', $name, $parts)) {
            throw new InvalidArgumentException('Season name must match YYYY/YYYY.');
        }

        $startYear = (int) $parts[1];
        $endYear = (int) $parts[2];

        return [
            'name' => $startYear.'/'.$endYear,
            'slug' => $startYear.'-'.$endYear,
            'starts_at' => Carbon::create($startYear, 9, 1)->toDateString(),
            'ends_at' => Carbon::create($endYear, 8, 31)->toDateString(),
        ];
    }

    public static function ensureForDate(DateTimeInterface $date): self
    {
        return self::ensureForName(self::attributesForDate($date)['name']);
    }

    public static function ensureForName(string $name): self
    {
        $attributes = self::attributesForName($name);

        return self::query()->updateOrCreate(
            ['slug' => $attributes['slug']],
            $attributes,
        );
    }

    public static function ensureCurrent(): self
    {
        $season = self::ensureForDate(now());
        $season->markAsCurrent();

        return $season;
    }

    public function markAsCurrent(): void
    {
        self::query()
            ->whereKeyNot($this->id)
            ->where('is_current', true)
            ->update(['is_current' => false]);

        $this->forceFill(['is_current' => true])->save();
    }

    /**
     * Create seasons from existing game dates and mark the current one.
     */
    public static function backfillFromGames(): void
    {
        Game::query()
            ->whereNotNull('data_hora')
            ->pluck('data_hora')
            ->unique()
            ->each(fn ($date) => self::ensureForDate(Carbon::parse($date)));

        self::ensureCurrent();
    }

    public static function current(): ?self
    {
        return self::query()->current()->first();
    }

    /**
     * @param  Builder<Season>  $query
     * @return Builder<Season>
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }
}
