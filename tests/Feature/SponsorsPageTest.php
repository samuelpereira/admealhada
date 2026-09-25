<?php

use App\Models\Season;
use App\Models\Sponsor;
use App\Models\Sport;
use App\Models\Team;

test('the sponsors page titles team sponsors as sponsors by team', function () {
    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();
    $sponsor = Sponsor::query()->create([
        'nome' => 'Parceiro da Equipa',
        'logo' => 'patrocinadores/logo.png',
        'link' => 'https://example.com',
        'tipo' => Sponsor::TIPO_PRINCIPAL,
        'ordem' => 0,
        'ativo' => true,
    ]);
    $team->sponsors()->attach($sponsor);

    Sponsor::query()->create([
        'nome' => 'Parceiro do Clube',
        'logo' => 'patrocinadores/clube.png',
        'link' => 'https://example.com/clube',
        'tipo' => Sponsor::TIPO_GERAL,
        'ordem' => 0,
        'ativo' => true,
    ]);

    $this->get(route('patrocinadores'))
        ->assertOk()
        ->assertSee('Patrocinadores por Equipa')
        ->assertDontSee('Patrocinadores principais')
        ->assertDontSee('Patrocinadores gerais');

    $this->get($team->publicShowUrl())
        ->assertOk()
        ->assertSeeInOrder(['Patrocinadores da Equipa', 'Próximos jogos']);
});

test('the team page shows an inactive principal sponsor', function () {
    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();
    $sponsor = Sponsor::query()->create([
        'nome' => 'Parceiro Inativo',
        'logo' => 'patrocinadores/inativo.png',
        'link' => 'https://example.com/inativo',
        'tipo' => Sponsor::TIPO_PRINCIPAL,
        'ordem' => 0,
        'ativo' => false,
    ]);
    $team->sponsors()->attach($sponsor);

    $this->get($team->publicShowUrl())
        ->assertOk()
        ->assertSee('Parceiro Inativo');

    $this->get(route('patrocinadores'))
        ->assertOk()
        ->assertDontSee('Parceiro Inativo');
});

test('the sponsors page lists only teams from the current season', function () {
    $sport = Sport::factory()->create();
    $currentTeam = Team::factory()->for($sport)->for(Season::ensureCurrent())->create([
        'nome' => 'Séniores época actual',
    ]);
    $archiveTeam = Team::factory()->for($sport)->for(Season::ensureForName('2024/2025'))->create([
        'nome' => 'Séniores época arquivo',
    ]);

    $currentSponsor = Sponsor::query()->create([
        'nome' => 'Parceiro Actual',
        'logo' => 'patrocinadores/actual.png',
        'link' => 'https://example.com/actual',
        'tipo' => Sponsor::TIPO_PRINCIPAL,
        'ordem' => 0,
        'ativo' => true,
    ]);
    $archiveSponsor = Sponsor::query()->create([
        'nome' => 'Parceiro Arquivo',
        'logo' => 'patrocinadores/arquivo.png',
        'link' => 'https://example.com/arquivo',
        'tipo' => Sponsor::TIPO_PRINCIPAL,
        'ordem' => 0,
        'ativo' => true,
    ]);

    $currentTeam->sponsors()->attach($currentSponsor);
    $archiveTeam->sponsors()->attach($archiveSponsor);

    $this->get(route('patrocinadores'))
        ->assertOk()
        ->assertSee('Séniores época actual')
        ->assertSee('Parceiro Actual')
        ->assertDontSee('Séniores época arquivo')
        ->assertDontSee('Parceiro Arquivo');
});
