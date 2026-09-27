<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->string('miniatura')->nullable()->after('caminho');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->string('capa_miniatura')->nullable()->after('capa');
        });
    }

    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('miniatura');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('capa_miniatura');
        });
    }
};
