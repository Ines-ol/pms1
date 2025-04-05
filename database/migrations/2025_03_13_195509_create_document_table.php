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
            Schema::create('document', function (Blueprint $table) {
            $table->id('ID_DOCUMENT'); // Clé primaire auto-incrémentée
            $table->integer('ID_USER')->nullable(); // Clé étrangère vers la table `user` (nullable)
            $table->enum('DOCUMENT_TYPE', ['contract', 'invoice', 'tax']); // Type de document (enum)
            $table->string('FILE_PATH', 255); // Chemin du fichier
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
        Schema::dropIfExists('document');
    }
};
