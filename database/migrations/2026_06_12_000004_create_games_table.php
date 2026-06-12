<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('adversario');
            $table->dateTime('data_hora');
            $table->string('local')->nullable();
            $table->string('casa_fora', 4)->default('casa');
            $table->string('competicao')->nullable();
            $table->unsignedInteger('jornada')->nullable();
            $table->unsignedInteger('pontos_casa')->nullable();
            $table->unsignedInteger('pontos_fora')->nullable();
            $table->string('estado')->default('agendado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
