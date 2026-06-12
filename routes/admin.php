<?php

use App\Livewire\Admin\Albums\AlbumForm;
use App\Livewire\Admin\Albums\AlbumsIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Games\GameForm;
use App\Livewire\Admin\Games\GamesIndex;
use App\Livewire\Admin\News\NewsForm;
use App\Livewire\Admin\News\NewsIndex;
use App\Livewire\Admin\Players\PlayerForm;
use App\Livewire\Admin\Players\PlayersIndex;
use App\Livewire\Admin\Sponsors\SponsorForm;
use App\Livewire\Admin\Sponsors\SponsorsIndex;
use App\Livewire\Admin\Sports\SportForm;
use App\Livewire\Admin\Sports\SportsIndex;
use App\Livewire\Admin\Teams\TeamForm;
use App\Livewire\Admin\Teams\TeamsIndex;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::livewire('/', Dashboard::class)->name('dashboard');

        Route::livewire('noticias', NewsIndex::class)->name('news.index');
        Route::livewire('noticias/nova', NewsForm::class)->name('news.create');
        Route::livewire('noticias/{news}/editar', NewsForm::class)->name('news.edit');

        Route::livewire('modalidades', SportsIndex::class)->name('sports.index');
        Route::livewire('modalidades/nova', SportForm::class)->name('sports.create');
        Route::livewire('modalidades/{sport}/editar', SportForm::class)->name('sports.edit');

        Route::livewire('equipas', TeamsIndex::class)->name('teams.index');
        Route::livewire('equipas/nova', TeamForm::class)->name('teams.create');
        Route::livewire('equipas/{team}/editar', TeamForm::class)->name('teams.edit');

        Route::livewire('jogadores', PlayersIndex::class)->name('players.index');
        Route::livewire('jogadores/novo', PlayerForm::class)->name('players.create');
        Route::livewire('jogadores/{player}/editar', PlayerForm::class)->name('players.edit');

        Route::livewire('jogos', GamesIndex::class)->name('games.index');
        Route::livewire('jogos/novo', GameForm::class)->name('games.create');
        Route::livewire('jogos/{game}/editar', GameForm::class)->name('games.edit');

        Route::livewire('galerias', AlbumsIndex::class)->name('albums.index');
        Route::livewire('galerias/nova', AlbumForm::class)->name('albums.create');
        Route::livewire('galerias/{album}/editar', AlbumForm::class)->name('albums.edit');

        Route::livewire('patrocinadores', SponsorsIndex::class)->name('sponsors.index');
        Route::livewire('patrocinadores/novo', SponsorForm::class)->name('sponsors.create');
        Route::livewire('patrocinadores/{sponsor}/editar', SponsorForm::class)->name('sponsors.edit');
    });
