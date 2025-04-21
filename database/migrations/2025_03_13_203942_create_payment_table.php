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
            $table->id('ID_PAYMENT');
            $table->unsignedBigInteger('ID_RESERVATION')->nullable();
            $table->string('FIRST_NAME', 100)->nullable();
            $table->string('LAST_NAME', 100)->nullable();
            $table->string('CARD_NUMBER', 20)->nullable();
            $table->string('EXPIRATION_DATE', 10)->nullable(); // Format: MM/YYYY
            $table->string('CVV', 4)->nullable();
            $table->enum('METHOD', ['cash', 'credit_card', 'bank_transfer', ])->nullable();
            $table->double('AMOUNT');
            $table->enum('STATUS', ['pending', 'completed'])->default('pending');
            $table->string('TRANSACTION_ID', 100)->nullable();
            $table->timestamp('PAYMENT_DATE')->nullable();
            
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