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
        Schema::create('employee', function (Blueprint $table) {
            $table->id('ID_EMPLOYEE'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_USER')->nullable(); // Clé étrangère vers la table `user` (nullable)
            $table->string('POSITION', 255)->nullable(); // Poste de l'employé (nullable)
            
            // Index sur la colonne ID_USER
            $table->index('ID_USER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
