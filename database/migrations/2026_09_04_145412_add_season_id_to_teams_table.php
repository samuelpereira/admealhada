<?php

use App\Models\Season;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->foreignId('season_id')->nullable()->after('id')->constrained()->restrictOnDelete();
        });

        $seasonId = Season::ensureCurrent()->id;

        DB::table('teams')->whereNull('season_id')->update(['season_id' => $seasonId]);

        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->unique(['season_id', 'slug']);
            $table->foreignId('season_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['season_id', 'slug']);
            $table->dropConstrainedForeignId('season_id');
            $table->unique('slug');
        });
    }
};
