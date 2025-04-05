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
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 20)->primary(); // Clé primaire
            $table->mediumText('value'); // Pas de longueur pour mediumText
            $table->integer('expiration'); // Pas de auto_increment
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 20)->primary(); // Clé primaire
            $table->string('owner', 20); // Propriétaire du verrou
            $table->integer('expiration'); // Pas de auto_increment
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};

