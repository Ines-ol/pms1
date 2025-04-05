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
        Schema::create('reservation', function (Blueprint $table) {
            $table->id('ID_RESERVATION'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_CLIENT')->nullable(); // Clé étrangère vers la table `client` (nullable)
            $table->unsignedBigInteger('ID_ROOM')->nullable(); // Clé étrangère vers la table `room` (nullable)
            $table->date('START_DATE'); // Date de début de la réservation
            $table->date('END_DATE'); // Date de fin de la réservation
            $table->enum('STATUS', ['pending', 'confirmed', 'cancelled'])->default('pending'); // Statut de la réservation (enum avec valeur par défaut)
            
            // Index sur les colonnes ID_CLIENT et ID_ROOM
            $table->index('ID_CLIENT');
            $table->index('ID_ROOM');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation');
    }
};
