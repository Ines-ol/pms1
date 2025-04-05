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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id('ID_INVOICE'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_RESERVATION')->nullable(); // Clé étrangère vers la table `reservation` (nullable)
            $table->double('AMOUNT'); // Montant de la facture
            $table->enum('STATUS', ['pending', 'paid'])->default('pending'); // Statut de la facture (enum avec valeur par défaut)
            
            // Index sur la colonne ID_RESERVATION
            $table->index('ID_RESERVATION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
