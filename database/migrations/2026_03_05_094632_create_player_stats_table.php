<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('player_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('player_id')->constrained('players');
            $table->foreignUuid('match_id')->nullable()->constrained('matches')->nullOnDelete();
            $table->foreignUuid('match_map_id')->nullable()->constrained('match_maps')->nullOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('region', 20)->nullable();
            $table->decimal('rating', 4, 2)->nullable();
            $table->decimal('acs', 6, 1)->nullable();
            $table->decimal('kd_ratio', 4, 2)->nullable();
            $table->decimal('kast', 5, 2)->nullable();
            $table->decimal('adr', 6, 1)->nullable();
            $table->decimal('kpr', 4, 2)->nullable();
            $table->decimal('headshot_pct', 5, 2)->nullable();
            $table->decimal('clutch_pct', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_stats');
    }
};
