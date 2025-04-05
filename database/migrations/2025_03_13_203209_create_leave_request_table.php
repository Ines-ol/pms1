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
        Schema::create('leave_request', function (Blueprint $table) {
            $table->id('ID_LEAVE_REQUEST'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_EMPLOYEE')->nullable(); // Clé étrangère vers la table `employee` (nullable)
            $table->date('START_DATE'); // Date de début du congé
            $table->date('END_DATE'); // Date de fin du congé
            $table->enum('STATUS', ['pending', 'approved', 'rejected'])->default('pending'); // Statut de la demande (enum avec valeur par défaut)
            
            // Index sur la colonne ID_EMPLOYEE
            $table->index('ID_EMPLOYEE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_request');
    }
};
