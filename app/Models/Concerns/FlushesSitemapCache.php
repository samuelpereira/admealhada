<?php

namespace App\Models\Concerns;

use App\Support\SitemapCache;

trait FlushesSitemapCache
{
    protected static function bootFlushesSitemapCache(): void
    {
        static::saved(fn () => SitemapCache::forget());
        static::deleted(fn () => SitemapCache::forget());
    }
}
