<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

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

    public function scopePublicadas(Builder $query): Builder
    {
        return $query->whereNotNull('publicado_em')
            ->where('publicado_em', '<=', now())
            ->orderByDesc('publicado_em');
    }
}
