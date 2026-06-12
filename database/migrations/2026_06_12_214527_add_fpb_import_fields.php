<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('fpb_url')->nullable()->after('competicao');
        });

        Schema::table('games', function (Blueprint $table) {
            $table->string('fpb_id')->nullable()->after('team_id');
            $table->unique(['team_id', 'fpb_id']);
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'fpb_id']);
            $table->dropColumn('fpb_id');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('fpb_url');
        });
    }
};
