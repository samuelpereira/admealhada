<?php

use App\Support\PersonalDataEncrypter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->widen('coaches', ['nome', 'data_nascimento', 'nacionalidade']);
        $this->widen('players', ['nome', 'data_nascimento']);
        $this->widen('social_bodies', ['nome']);

        PersonalDataEncrypter::coaches();
        PersonalDataEncrypter::players();
        PersonalDataEncrypter::socialBodies();
    }

    public function down(): void
    {
        PersonalDataEncrypter::decryptCoaches();
        PersonalDataEncrypter::decryptPlayers();
        PersonalDataEncrypter::decryptSocialBodies();

        $this->restore('coaches', [
            'nome' => 'string',
            'data_nascimento' => 'date',
            'nacionalidade' => 'string',
        ]);
        $this->restore('players', [
            'nome' => 'string',
            'data_nascimento' => 'date',
        ]);
        $this->restore('social_bodies', [
            'nome' => 'string',
        ]);
    }

    /**
     * @param  list<string>  $columns
     */
    private function widen(string $table, array $columns): void
    {
        Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
            foreach ($columns as $column) {
                $blueprint->text($column.'_plain')->nullable();
            }
        });

        foreach (DB::table($table)->orderBy('id')->get() as $row) {
            $values = [];

            foreach ($columns as $column) {
                $values[$column.'_plain'] = $row->{$column};
            }

            DB::table($table)->where('id', $row->id)->update($values);
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
            $blueprint->dropColumn($columns);
        });

        Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
            foreach ($columns as $column) {
                $blueprint->renameColumn($column.'_plain', $column);
            }
        });
    }

    /**
     * @param  array<string, string>  $columns
     */
    private function restore(string $table, array $columns): void
    {
        Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
            foreach ($columns as $column => $type) {
                if ($type === 'date') {
                    $blueprint->date($column.'_typed')->nullable();
                } else {
                    $blueprint->string($column.'_typed')->nullable();
                }
            }
        });

        foreach (DB::table($table)->orderBy('id')->get() as $row) {
            $values = [];

            foreach ($columns as $column => $type) {
                $values[$column.'_typed'] = $row->{$column};
            }

            DB::table($table)->where('id', $row->id)->update($values);
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
            $blueprint->dropColumn(array_keys($columns));
        });

        Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
            foreach ($columns as $column => $type) {
                $blueprint->renameColumn($column.'_typed', $column);
            }
        });
    }
};
