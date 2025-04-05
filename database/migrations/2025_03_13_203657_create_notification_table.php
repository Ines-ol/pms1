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
        Schema::create('notification', function (Blueprint $table) {
            $table->id('ID_NOTIFICATION'); // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('ID_USER')->nullable(); // Clé étrangère vers la table `user` (nullable)
            $table->text('MESSAGE'); // Message de la notification
            $table->enum('STATUS', ['unread', 'read'])->default('unread'); // Statut de la notification (enum avec valeur par défaut)
            $table->timestamp('CREATED_AT')->nullable()->useCurrent(); // Date de création (timestamp avec valeur par défaut)
            
            // Index sur la colonne ID_USER
            $table->index('ID_USER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};
