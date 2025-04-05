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
        Schema::create('service_request', function (Blueprint $table) {
            $table->id('ID_SERVICE_REQUEST'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_CLIENT')->nullable(); // Clé étrangère vers la table `client` (nullable)
            $table->text('DESCRIPTION'); // Description de la demande de service
            $table->enum('STATUS', ['pending', 'in_progress', 'completed'])->default('pending'); // Statut de la demande (enum avec valeur par défaut)
            
            // Index sur la colonne ID_CLIENT
            $table->index('ID_CLIENT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_request');
    }
};
