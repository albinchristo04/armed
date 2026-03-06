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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('home_team');
            $table->string('away_team')->nullable();
            $table->string('league')->nullable();
            $table->boolean('is_important')->default(false);
            $table->string('home_logo')->nullable();
            $table->string('away_logo')->nullable();
            $table->string('league_logo')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('kick_off')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
