<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

/**
 * @implements CastsAttributes<Carbon|null, Carbon|string|null>
 */
class EncryptedDate implements CastsAttributes
{
    public bool $withoutObjectCaching = true;

    public function get(Model $model, string $key, mixed $value, array $attributes): ?Carbon
    {
        $plain = $this->plain($value);

        if ($plain === null) {
            return null;
        }

        return Carbon::parse($plain)->startOfDay();
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString(Carbon::parse($value)->toDateString());
    }

    public function compare(Model $model, string $key, mixed $original, mixed $value): bool
    {
        return $this->plain($original) === $this->plain($value);
    }

    private function plain(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        return Crypt::decryptString($value);
    }
}
