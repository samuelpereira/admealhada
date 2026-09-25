<?php

use App\Models\Game;
use App\View\Components\Site\GameCard;

test('an away win shows the home score on top and the club score below', function () {
    $game = Game::factory()->make([
        'adversario' => 'AAC - Sub23',
        'casa_fora' => 'fora',
        'estado' => 'terminado',
        'pontos_casa' => 59,
        'pontos_fora' => 60,
        'data_hora' => '2026-01-15 18:00:00',
    ]);

    $this->component(GameCard::class, ['game' => $game, 'showTeam' => false])
        ->assertDontSee('Fora')
        ->assertSeeInOrder(['Vitória', 'AAC - Sub23', '59', 'AD Mealhada', '60']);
});

test('a home loss shows the club score on top and the away score below', function () {
    $game = Game::factory()->make([
        'adversario' => 'NDA Pombal',
        'casa_fora' => 'casa',
        'estado' => 'terminado',
        'pontos_casa' => 46,
        'pontos_fora' => 86,
        'data_hora' => '2026-01-15 18:00:00',
    ]);

    $this->component(GameCard::class, ['game' => $game, 'showTeam' => false])
        ->assertDontSee('Casa')
        ->assertSeeInOrder(['Derrota', 'AD Mealhada', '46', 'NDA Pombal', '86']);
});

test('a scheduled home game shows casa and hides the result', function () {
    $game = Game::factory()->make([
        'adversario' => 'NDA Pombal',
        'casa_fora' => 'casa',
        'estado' => 'agendado',
        'data_hora' => '2026-10-15 18:00:00',
    ]);

    $this->component(GameCard::class, ['game' => $game, 'showTeam' => false])
        ->assertSee('Casa')
        ->assertDontSee('Fora')
        ->assertDontSee('Vitória')
        ->assertDontSee('Derrota');
});

test('a scheduled away game shows fora and hides the result', function () {
    $game = Game::factory()->make([
        'adversario' => 'AAC - Sub23',
        'casa_fora' => 'fora',
        'estado' => 'agendado',
        'data_hora' => '2026-10-15 18:00:00',
    ]);

    $this->component(GameCard::class, ['game' => $game, 'showTeam' => false])
        ->assertSee('Fora')
        ->assertDontSee('Casa')
        ->assertDontSee('Vitória')
        ->assertDontSee('Derrota');
});
