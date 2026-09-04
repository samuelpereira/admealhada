<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class SitemapCache
{
    public const KEY = 'sitemap.xml';

    public static function forget(): void
    {
        Cache::forget(self::KEY);
    }
}
