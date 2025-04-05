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
        Schema::create('maintenance_request', function (Blueprint $table) {
            $table->id('ID_REQUEST'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_PROPERTY_MANAGER')->nullable(); // Clé étrangère vers la table `property_manager` (nullable)
            $table->text('DESCRIPTION'); // Description de la demande de maintenance
            $table->enum('STATUS', ['pending', 'in_progress', 'completed'])->default('pending'); // Statut de la demande (enum avec valeur par défaut)
            
            // Index sur la colonne ID_PROPERTY_MANAGER
            $table->index('ID_PROPERTY_MANAGER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_request');
    }
};
