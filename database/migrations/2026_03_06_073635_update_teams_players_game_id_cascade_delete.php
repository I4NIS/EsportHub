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
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->foreign('game_id')->references('id')->on('games');
        });

        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['game_id']);
            $table->foreign('game_id')->references('id')->on('games');
        });
    }
};
