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
        Schema::create('room', function (Blueprint $table) {
            $table->id('ID_ROOM'); // Clé primaire auto-incrémentée
            $table->enum('TYPE', ['single', 'double', 'suite']); // Type de chambre (enum)
            $table->double('PRICE'); // Prix de la chambre
            $table->boolean('AVAILABLE')->default(true); // Disponibilité de la chambre (booléen avec valeur par défaut)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room');
    }
};
