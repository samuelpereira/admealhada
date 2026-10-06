<?php

namespace App\Support;

class NameFilter
{
    public static function matches(string $name, string $term): bool
    {
        $term = trim($term);

        if ($term === '') {
            return true;
        }

        return mb_stripos(self::fold($name), self::fold($term)) !== false;
    }

    private static function fold(string $value): string
    {
        return strtr(mb_strtolower($value), [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a',
            'é' => 'e', 'ê' => 'e',
            'í' => 'i',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ú' => 'u', 'ü' => 'u',
            'ç' => 'c',
        ]);
    }
}
