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
        Schema::create('client', function (Blueprint $table) {
            $table->id('ID_CLIENT'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_USER')->nullable(); // Clé étrangère vers la table `user` (nullable)
            $table->string('ADDRESS', 255)->nullable(); 
            $table->string('PHONE', 20)->nullable(); 
            $table->string('BIRTHDAY', 255)->nullable();
            
            // Index sur la colonne ID_USER
            $table->index('ID_USER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client');
    }
};
