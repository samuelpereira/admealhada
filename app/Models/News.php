<?php

namespace App\Models;

use App\Models\Concerns\FlushesSitemapCache;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $titulo
 * @property string $slug
 * @property string|null $resumo
 * @property string|null $corpo
 * @property string|null $capa
 * @property string $categoria
 * @property bool $destaque
 * @property Carbon|null $publicado_em
 *
 * @use HasFactory<NewsFactory>
 */
class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use FlushesSitemapCache, HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'titulo',
        'slug',
        'resumo',
        'corpo',
        'capa',
        'categoria',
        'destaque',
        'publicado_em',
    ];

    protected function casts(): array
    {
        return [
            'destaque' => 'boolean',
            'publicado_em' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<News>  $query
     * @return Builder<News>
     */
    public function scopePublicadas(Builder $query): Builder
    {
        return $query->whereNotNull('publicado_em')
            ->where('publicado_em', '<=', now())
            ->orderByDesc('publicado_em');
    }
}
