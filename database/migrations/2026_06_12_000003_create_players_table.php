<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('nome');
            $table->unsignedInteger('numero')->nullable();
            $table->string('posicao')->nullable();
            $table->unsignedSmallInteger('ano_nascimento')->nullable();
            $table->string('foto')->nullable();
            $table->string('papel')->default('jogador');
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
