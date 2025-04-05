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
        Schema::create('sickness_record', function (Blueprint $table) {
            $table->id('ID_SICKNESS_RECORD'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_EMPLOYEE')->nullable(); // Clé étrangère vers la table `employee` (nullable)
            $table->date('START_DATE'); // Date de début de la maladie
            $table->date('END_DATE'); // Date de fin de la maladie
            $table->text('DESCRIPTION')->nullable(); // Description de la maladie (nullable)
            
            // Index sur la colonne ID_EMPLOYEE
            $table->index('ID_EMPLOYEE');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sickness_record');
    }
};
