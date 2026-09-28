<?php

use App\Livewire\Admin\Coaches\CoachesIndex;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Season;
use App\Models\SocialBody;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Support\PersonalDataEncrypter;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

test('coach name birth date and nationality are encrypted at rest', function () {
    $coach = Coach::factory()->create([
        'nome' => 'Rui Costa',
        'data_nascimento' => '1980-04-12',
        'nacionalidade' => 'Portugal',
    ]);

    $raw = DB::table('coaches')->where('id', $coach->id)->first();

    expect($raw->nome)->not->toBe('Rui Costa')
        ->and($raw->data_nascimento)->not->toBe('1980-04-12')
        ->and($raw->nacionalidade)->not->toBe('Portugal')
        ->and($coach->fresh()->nome)->toBe('Rui Costa')
        ->and($coach->fresh()->data_nascimento?->toDateString())->toBe('1980-04-12')
        ->and($coach->fresh()->nacionalidade)->toBe('Portugal');
});

test('player name and birth date are encrypted at rest', function () {
    $player = Player::factory()->for(Team::factory()->for(Sport::factory()))->create([
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-03-17',
    ]);

    $raw = DB::table('players')->where('id', $player->id)->first();

    expect($raw->nome)->not->toBe('Miguel Costa')
        ->and($raw->data_nascimento)->not->toBe('2011-03-17')
        ->and($player->fresh()->nome)->toBe('Miguel Costa')
        ->and($player->fresh()->data_nascimento?->toDateString())->toBe('2011-03-17');
});

test('a social body name is encrypted at rest and still shown on the club page', function () {
    SocialBody::query()->create([
        'categoria' => 'direcao',
        'cargo' => 'Vogal',
        'nome' => 'Bruno Alves',
        'ordem' => 1,
        'ativo' => true,
    ]);
    SocialBody::query()->create([
        'categoria' => 'direcao',
        'cargo' => 'Presidente',
        'nome' => 'Ana Dias',
        'ordem' => 1,
        'ativo' => true,
    ]);

    $raw = DB::table('social_bodies')->where('cargo', 'Presidente')->first();

    expect($raw->nome)->not->toBe('Ana Dias');

    $this->get(route('clube'))
        ->assertOk()
        ->assertSeeInOrder(['Ana Dias', 'Bruno Alves']);
});

test('plaintext personal fields already stored are encrypted in place', function () {
    $coach = Coach::factory()->create();
    $team = Team::factory()->for(Sport::factory())->for(Season::ensureCurrent())->create();
    $player = Player::factory()->for($team)->create();
    $membro = SocialBody::query()->create([
        'categoria' => 'direcao',
        'cargo' => 'Presidente',
        'nome' => 'Temporario',
        'ordem' => 1,
        'ativo' => true,
    ]);

    DB::table('coaches')->where('id', $coach->id)->update([
        'nome' => 'Rui Costa',
        'data_nascimento' => '1980-04-12',
        'nacionalidade' => 'Portugal',
    ]);
    DB::table('players')->where('id', $player->id)->update([
        'nome' => 'Miguel Costa',
        'data_nascimento' => '2011-03-17',
    ]);
    DB::table('social_bodies')->where('id', $membro->id)->update([
        'nome' => 'Ana Dias',
    ]);

    PersonalDataEncrypter::coaches();
    PersonalDataEncrypter::players();
    PersonalDataEncrypter::socialBodies();

    expect($coach->fresh()->nome)->toBe('Rui Costa')
        ->and($coach->fresh()->data_nascimento?->toDateString())->toBe('1980-04-12')
        ->and($coach->fresh()->nacionalidade)->toBe('Portugal')
        ->and($player->fresh()->nome)->toBe('Miguel Costa')
        ->and($player->fresh()->data_nascimento?->toDateString())->toBe('2011-03-17')
        ->and($membro->fresh()->nome)->toBe('Ana Dias')
        ->and(DB::table('coaches')->where('id', $coach->id)->value('nome'))->not->toBe('Rui Costa')
        ->and(DB::table('players')->where('id', $player->id)->value('nome'))->not->toBe('Miguel Costa')
        ->and(DB::table('social_bodies')->where('id', $membro->id)->value('nome'))->not->toBe('Ana Dias');
});

test('repeating an encrypted name does not mark the record as changed', function () {
    $coach = Coach::factory()->create(['nome' => 'Rui Costa', 'data_nascimento' => '1980-04-12']);

    $coach->nome = 'Rui Costa';
    $coach->data_nascimento = '1980-04-12';

    expect($coach->isDirty('nome'))->toBeFalse()
        ->and($coach->isDirty('data_nascimento'))->toBeFalse();

    $coach->nome = 'Ana Lopes';

    expect($coach->isDirty('nome'))->toBeTrue();
});

test('coaches are listed alphabetically by decrypted name', function () {
    $this->actingAs(User::factory()->admin()->create());

    Coach::factory()->create(['nome' => 'Rui Costa']);
    Coach::factory()->create(['nome' => 'Ana Lopes']);

    Livewire::test(CoachesIndex::class)
        ->assertSeeInOrder(['Ana Lopes', 'Rui Costa']);
});
