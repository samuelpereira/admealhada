<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'nome',
        'logo',
        'link',
        'ordem',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ordem' => 'integer',
            'ativo' => 'boolean',
        ];
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query
            ->where('ativo', true)
            ->whereNotNull('logo')
            ->orderBy('ordem')
            ->orderBy('nome');
    }
}
