<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'titulo',
        'slug',
        'descricao',
        'capa',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('ordem');
    }
}
