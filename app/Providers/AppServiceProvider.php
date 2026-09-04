<?php

namespace App\Providers;

use App\Models\News;
use App\Models\Season;
use App\Models\Sponsor;
use App\Models\Team;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View as IlluminateView;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureViewComposers();
    }

    protected function configureViewComposers(): void
    {
        View::composer('components.layouts.site', $this->composeSiteLayout(...));
        View::composer('layouts.auth.split', $this->composeAuthSplit(...));
    }

    protected function composeAuthSplit(IlluminateView $view): void
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $view->with([
            'inspiringQuote' => trim((string) $message),
            'inspiringAuthor' => trim((string) $author),
        ]);
    }

    protected function composeSiteLayout(IlluminateView $view): void
    {
        $view->with([
            'patrocinadores' => Sponsor::query()->ativos()->get(),
            'navLinks' => [
                ['route' => 'clube', 'label' => 'Clube'],
                ['route' => 'equipas', 'label' => 'Equipas'],
                ['route' => 'patrocinadores', 'label' => 'Patrocinadores'],
                ['route' => 'calendario', 'label' => 'Calendário'],
                ['route' => 'noticias', 'label' => 'Notícias'],
                ['route' => 'galeria', 'label' => 'Galeria'],
                ['route' => 'contactos', 'label' => 'Contactos'],
            ],
            ...$this->siteSeo($view),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function siteSeo(IlluminateView $view): array
    {
        $data = $view->getData();
        $siteName = config('site.contact.name', config('app.name'));
        $routeName = request()->route()?->getName();
        $routeTitle = trim((string) ($data['title'] ?? ''));
        $news = request()->route('news');
        $team = request()->route('team');
        $season = request()->route('season');

        $pageTitle = filled($routeTitle)
            ? (Str::contains($routeTitle, config('app.name')) ? $routeTitle : $routeTitle.' - '.$siteName)
            : $siteName.' - Basquetebol';

        $defaultDescriptions = [
            'home' => 'Site oficial da Academia Desportiva da Mealhada: basquetebol de formação, competição, equipas, calendário, notícias, galeria e contactos.',
            'clube' => 'Conhece a Academia Desportiva da Mealhada, a estrutura institucional, órgãos sociais e projecto desportivo de basquetebol na Mealhada.',
            'equipas' => 'Equipas de basquetebol da AD Mealhada, do minibasquete aos escalões de formação e seniores.',
            'patrocinadores' => 'Patrocinadores e parceiros oficiais da Academia Desportiva da Mealhada.',
            'calendario' => 'Calendário de jogos da AD Mealhada, com partidas futuras e resultados sincronizados por API.',
            'noticias' => 'Notícias oficiais da Academia Desportiva da Mealhada e actualizações das equipas de basquetebol.',
            'galeria' => 'Galeria de fotografias da AD Mealhada: jogos, equipas, torneios e momentos do clube.',
            'contactos' => 'Contactos da Academia Desportiva da Mealhada para inscrições, informações, parcerias e comunicação institucional.',
        ];

        $pageDescription = $data['description']
            ?? $defaultDescriptions[$routeName ?? '']
            ?? $defaultDescriptions['home'];
        $shareImage = Vite::asset('resources/images/hero-hand-ball.png');
        $ogType = 'website';

        if ($news instanceof News) {
            $pageTitle = $news->titulo.' - '.$siteName;
            $pageDescription = $news->resumo ?: Str::limit(trim(strip_tags((string) $news->corpo)), 155);
            $ogType = 'article';

            if (filled($news->capa)) {
                $shareImage = url(Storage::url($news->capa));
            }
        }

        if ($team instanceof Team) {
            $archiveSeason = $season instanceof Season && ! $season->is_current
                ? $season
                : ($team->season && ! $team->season->is_current ? $team->season : null);

            $pageTitle = $archiveSeason
                ? $team->nome.' · '.$archiveSeason->name.' - '.$siteName
                : $team->nome.' - '.$siteName;
            $pageDescription = $team->descricao ?: 'Plantel, equipa técnica, competição e informação da equipa '.$team->nome.' da AD Mealhada.';

            if (filled($team->foto)) {
                $shareImage = url(Storage::url($team->foto));
            }
        } elseif ($season instanceof Season) {
            $pageTitle = 'Equipas · '.$season->name.' - '.$siteName;
            $pageDescription = 'Equipas de basquetebol da AD Mealhada na época '.$season->name.'.';
        }

        $sameAs = array_values(array_filter([
            config('site.contact.facebook'),
            config('site.contact.instagram'),
        ]));

        return [
            'siteName' => $siteName,
            'pageTitle' => $pageTitle,
            'pageDescription' => Str::limit(trim((string) $pageDescription), 160),
            'canonicalUrl' => url()->current(),
            'shareImage' => $shareImage,
            'ogType' => $ogType,
            'organizationSchema' => [
                '@context' => 'https://schema.org',
                '@type' => 'SportsOrganization',
                'name' => $siteName,
                'alternateName' => ['AD Mealhada', 'Academia Desportiva da Mealhada Basquetebol'],
                'url' => url('/'),
                'logo' => Vite::asset('resources/images/logo.png'),
                'image' => $shareImage,
                'sport' => 'Basketball',
                'email' => config('site.contact.email'),
                'telephone' => config('site.contact.phone'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => str_replace("\n", ', ', (string) config('site.contact.address')),
                    'addressCountry' => 'PT',
                ],
                'sameAs' => $sameAs,
            ],
        ];
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
