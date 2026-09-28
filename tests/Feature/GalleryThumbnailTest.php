<?php

use App\Livewire\Admin\Albums\AlbumForm;
use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use App\Support\GalleryThumbnail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('uploading a gallery photo stores a thumbnail for the grid', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create());

    $album = Album::factory()->create([
        'titulo' => 'Jogo contra o Ovarense',
        'slug' => 'jogo-contra-o-ovarense',
    ]);

    Livewire::test(AlbumForm::class, ['album' => $album])
        ->set('novasFotos', [UploadedFile::fake()->image('foto.jpg', 1200, 800)])
        ->call('guardarFotos')
        ->assertHasNoErrors();

    $photo = $album->photos()->first();

    expect($photo)->not->toBeNull()
        ->and($photo->miniatura)->not->toBeNull()
        ->and($photo->miniatura)->not->toBe($photo->caminho);

    [$width, $height] = getimagesize(Storage::disk('public')->path($photo->miniatura));

    expect(max($width, $height))->toBe(GalleryThumbnail::MAX_EDGE);

    $this->get(route('galeria.show', $album))
        ->assertOk()
        ->assertSee(Storage::url($photo->miniatura), false)
        ->assertSee(basename((string) $photo->caminho), false);

    Livewire::test(AlbumForm::class, ['album' => $album->fresh()])
        ->call('definirCapa', $photo->id);

    expect($album->fresh()->capa_miniatura)->toBe($photo->miniatura);

    Livewire::test(AlbumForm::class, ['album' => $album->fresh()])
        ->call('removerCapa');

    Storage::disk('public')->assertExists($photo->caminho);
    Storage::disk('public')->assertExists($photo->miniatura);
});

test('uploading an album cover stores a thumbnail for the gallery list', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(AlbumForm::class)
        ->set('titulo', 'Apresentação')
        ->set('capa', UploadedFile::fake()->image('capa.jpg', 900, 600))
        ->call('save')
        ->assertHasNoErrors();

    $album = Album::query()->where('titulo', 'Apresentação')->first();

    expect($album)->not->toBeNull()
        ->and($album->capa_miniatura)->not->toBeNull()
        ->and($album->capa_miniatura)->not->toBe($album->capa);

    $this->get(route('galeria'))
        ->assertOk()
        ->assertSee(Storage::url($album->capa_miniatura), false);
});

test('the admin gallery shows four photos per row', function () {
    $this->actingAs(User::factory()->admin()->create());

    $album = Album::factory()->create();
    Photo::factory()->for($album)->create();

    Livewire::test(AlbumForm::class, ['album' => $album])
        ->assertSee('lg:grid-cols-4', false);
});
