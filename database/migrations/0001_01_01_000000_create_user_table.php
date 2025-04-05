<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id('ID_USER'); // Clé primaire auto-incrémentée
            $table->string('NAME', 255); // Nom de l'utilisateur
            $table->string('EMAIL', 191)->unique(); // Email unique
            $table->string('PASSWORD', 255); // Mot de passe
            $table->enum('ROLE', ['client', 'employee', 'admin', 'manager']); // Rôle de l'utilisateur
            $table->timestamps(); // Ajoute les champs created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
};
