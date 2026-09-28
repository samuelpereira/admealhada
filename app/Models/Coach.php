<?php

namespace App\Models;

use App\Casts\EncryptedDate;
use App\Casts\EncryptedString;
use Database\Factories\CoachFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nome
 * @property Carbon|null $data_nascimento
 * @property string|null $nacionalidade
 * @property string|null $grau
 * @property string|null $foto
 *
 * @use HasFactory<CoachFactory>
 */
class Coach extends Model
{
    /** @use HasFactory<CoachFactory> */
    use HasFactory;

    protected $fillable = [
        'nome',
        'data_nascimento',
        'nacionalidade',
        'grau',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'nome' => EncryptedString::class,
            'data_nascimento' => EncryptedDate::class,
            'nacionalidade' => EncryptedString::class,
        ];
    }

    /** @return BelongsToMany<Team, $this> */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->orderBy('teams.ordem');
    }
}
