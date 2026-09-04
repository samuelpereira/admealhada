<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait GeneratesSlugs
{
    /**
     * Generate a slug that is unique within the given table, optionally scoped by extra columns.
     *
     * @param  array<string, mixed>  $constraints
     */
    protected function uniqueSlug(string $source, string $table, ?int $ignoreId = null, string $column = 'slug', array $constraints = []): string
    {
        $base = Str::slug($source);

        if ($base === '') {
            $base = 'item';
        }

        $slug = $base;
        $suffix = 2;

        while (
            DB::table($table)
                ->where($column, $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->when($constraints !== [], fn ($query) => $query->where($constraints))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
