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
        Schema::create('property_manager', function (Blueprint $table) {
            $table->id('ID_PROPERTY_MANAGER'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_USER')->nullable(); // Clé étrangère vers la table `user` (nullable)
            
            // Index sur la colonne ID_USER
            $table->index('ID_USER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_manager');
    }
};
