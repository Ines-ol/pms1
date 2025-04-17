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
        Schema::create('admin', function (Blueprint $table) {
            $table->id('ID_ADMIN');
            $table->unsignedBigInteger('ID_USER')->nullable(); 
            $table->string('ADDRESS', 255)->nullable(); 
            $table->string('PHONE', 20)->nullable(); 
            $table->string('BIRTHDAY', 255)->nullable();
            $table->timestamps();

            $table->index('ID_USER');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin');
    }
};
