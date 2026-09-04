<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUrl
{
    public static function resolve(mixed $src): ?string
    {
        if (! filled($src) || ! is_string($src)) {
            return null;
        }

        return Str::startsWith($src, ['http://', 'https://', '/'])
            ? $src
            : Storage::url($src);
    }

    public static function absolute(mixed $src, string $fallback): string
    {
        $resolved = self::resolve($src);

        return $resolved ? url($resolved) : $fallback;
    }
}
