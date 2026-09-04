<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\News;
use App\Models\Season;
use App\Models\Team;
use App\Support\SitemapCache;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $xml = Cache::remember(SitemapCache::KEY, now()->addDay(), function (): string {
            $urls = collect([
                ['loc' => route('home'), 'priority' => '1.0'],
                ['loc' => route('clube'), 'priority' => '0.8'],
                ['loc' => route('equipas'), 'priority' => '0.9'],
                ['loc' => route('patrocinadores'), 'priority' => '0.7'],
                ['loc' => route('calendario'), 'priority' => '0.8'],
                ['loc' => route('noticias'), 'priority' => '0.8'],
                ['loc' => route('galeria'), 'priority' => '0.7'],
                ['loc' => route('contactos'), 'priority' => '0.7'],
            ]);

            Season::query()
                ->where('is_current', false)
                ->whereHas('teams')
                ->orderByDesc('starts_at')
                ->get()
                ->each(fn (Season $season) => $urls->push([
                    'loc' => $season->publicIndexUrl(),
                    'priority' => '0.6',
                ]));

            Team::query()
                ->with('season')
                ->orderBy('ordem')
                ->get()
                ->each(fn (Team $team) => $urls->push([
                    'loc' => $team->publicShowUrl(),
                    'lastmod' => $team->updated_at?->toAtomString(),
                    'priority' => $team->season?->is_current ? '0.8' : '0.5',
                ]));

            News::query()
                ->publicadas()
                ->get()
                ->each(fn (News $news) => $urls->push([
                    'loc' => route('noticias.show', $news),
                    'lastmod' => $news->updated_at?->toAtomString(),
                    'priority' => '0.7',
                ]));

            Album::query()
                ->latest('data')
                ->latest('updated_at')
                ->get()
                ->each(fn (Album $album) => $urls->push([
                    'loc' => route('galeria.show', $album),
                    'lastmod' => $album->updated_at?->toAtomString(),
                    'priority' => '0.6',
                ]));

            return view('sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
