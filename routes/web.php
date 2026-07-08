<?php

use App\Livewire\Site\About;
use App\Livewire\Site\AlbumShow;
use App\Livewire\Site\Contact;
use App\Livewire\Site\Gallery;
use App\Livewire\Site\Home;
use App\Livewire\Site\NewsIndex;
use App\Livewire\Site\NewsShow;
use App\Livewire\Site\Schedule;
use App\Livewire\Site\Sponsors;
use App\Livewire\Site\Teams;
use App\Livewire\Site\TeamShow;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/clube', About::class)->name('clube');
Route::get('/equipas', Teams::class)->name('equipas');
Route::get('/equipas/{team}', TeamShow::class)->name('equipas.show');
Route::get('/patrocinadores', Sponsors::class)->name('patrocinadores');
Route::get('/calendario', Schedule::class)->name('calendario');
Route::get('/noticias', NewsIndex::class)->name('noticias');
Route::get('/noticias/{news}', NewsShow::class)->name('noticias.show');
Route::get('/galeria', Gallery::class)->name('galeria');
Route::get('/galeria/{album}', AlbumShow::class)->name('galeria.show');
Route::get('/contactos', Contact::class)->name('contactos');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', 'admin')->name('dashboard');
});

require __DIR__.'/admin.php';
require __DIR__.'/settings.php';
