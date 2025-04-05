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
        Schema::create('payment', function (Blueprint $table) {
            $table->id('ID_PAYMENT'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_RESERVATION')->nullable(); // Clé étrangère vers la table `reservation` (nullable)
            $table->double('AMOUNT'); // Montant du paiement
            $table->enum('METHOD', ['cash', 'credit_card', 'bank_transfer']); // Méthode de paiement (enum)
            $table->enum('STATUS', ['pending', 'completed'])->default('pending'); // Statut du paiement (enum avec valeur par défaut)
            
            // Index sur la colonne ID_RESERVATION
            $table->index('ID_RESERVATION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
