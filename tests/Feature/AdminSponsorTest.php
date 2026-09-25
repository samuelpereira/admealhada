<?php

use App\Livewire\Admin\Sponsors\SponsorsIndex;
use App\Models\Sponsor;
use App\Models\User;
use Livewire\Livewire;

test('the admin sponsor list is ordered by status then by order', function () {
    $this->actingAs(User::factory()->admin()->create());

    Sponsor::query()->create([
        'nome' => 'Inativo primeiro',
        'tipo' => Sponsor::TIPO_PRINCIPAL,
        'ordem' => 0,
        'ativo' => false,
    ]);
    Sponsor::query()->create([
        'nome' => 'Ativo segundo',
        'tipo' => Sponsor::TIPO_GERAL,
        'ordem' => 2,
        'ativo' => true,
    ]);
    Sponsor::query()->create([
        'nome' => 'Ativo primeiro',
        'tipo' => Sponsor::TIPO_PRINCIPAL,
        'ordem' => 1,
        'ativo' => true,
    ]);

    Livewire::test(SponsorsIndex::class)
        ->assertSeeInOrder(['Ativo primeiro', 'Ativo segundo', 'Inativo primeiro']);
});
