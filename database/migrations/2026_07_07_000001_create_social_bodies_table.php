<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_bodies', function (Blueprint $table) {
            $table->id();
            $table->string('categoria');
            $table->string('cargo');
            $table->string('nome');
            $table->unsignedInteger('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index(['categoria', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_bodies');
    }
};
