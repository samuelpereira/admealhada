<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class PersonalDataEncrypter
{
    public static function coaches(): void
    {
        foreach (DB::table('coaches')->orderBy('id')->get() as $row) {
            DB::table('coaches')->where('id', $row->id)->update([
                'nome' => self::string($row->nome),
                'data_nascimento' => self::date($row->data_nascimento),
                'nacionalidade' => self::nullableString($row->nacionalidade),
            ]);
        }
    }

    public static function players(): void
    {
        foreach (DB::table('players')->orderBy('id')->get() as $row) {
            DB::table('players')->where('id', $row->id)->update([
                'nome' => self::string($row->nome),
                'data_nascimento' => self::date($row->data_nascimento),
            ]);
        }
    }

    public static function socialBodies(): void
    {
        foreach (DB::table('social_bodies')->orderBy('id')->get() as $row) {
            DB::table('social_bodies')->where('id', $row->id)->update([
                'nome' => self::string($row->nome),
            ]);
        }
    }

    public static function decryptCoaches(): void
    {
        foreach (DB::table('coaches')->orderBy('id')->get() as $row) {
            DB::table('coaches')->where('id', $row->id)->update([
                'nome' => self::openString($row->nome),
                'data_nascimento' => self::openDate($row->data_nascimento),
                'nacionalidade' => self::openNullableString($row->nacionalidade),
            ]);
        }
    }

    public static function decryptPlayers(): void
    {
        foreach (DB::table('players')->orderBy('id')->get() as $row) {
            DB::table('players')->where('id', $row->id)->update([
                'nome' => self::openString($row->nome),
                'data_nascimento' => self::openDate($row->data_nascimento),
            ]);
        }
    }

    public static function decryptSocialBodies(): void
    {
        foreach (DB::table('social_bodies')->orderBy('id')->get() as $row) {
            DB::table('social_bodies')->where('id', $row->id)->update([
                'nome' => self::openString($row->nome),
            ]);
        }
    }

    private static function string(mixed $value): string
    {
        return Crypt::encryptString((string) $value);
    }

    private static function nullableString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString((string) $value);
    }

    private static function date(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::encryptString(Carbon::parse((string) $value)->toDateString());
    }

    private static function openString(mixed $value): string
    {
        return Crypt::decryptString((string) $value);
    }

    private static function openNullableString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Crypt::decryptString((string) $value);
    }

    private static function openDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse(Crypt::decryptString((string) $value))->toDateString();
    }
}
