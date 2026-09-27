<?php

use App\Models\Album;

test('the gallery description keeps line breaks and bold text', function () {
    $album = Album::factory()->create([
        'titulo' => 'Jogo de apresentação',
        'slug' => 'jogo-de-apresentacao',
        'descricao' => "Primeira linha\nA **AD** Mealhada venceu.\n<script>alert(1)</script>",
    ]);

    $this->get(route('galeria.show', $album))
        ->assertOk()
        ->assertSee('Primeira linha<br>', false)
        ->assertSee('<strong>AD</strong>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);

    $this->get(route('galeria'))
        ->assertOk()
        ->assertSee('<strong>AD</strong>', false)
        ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
});
