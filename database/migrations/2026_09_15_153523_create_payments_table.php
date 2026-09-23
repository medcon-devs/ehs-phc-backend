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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Links payment to users.id
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Stripe PaymentIntent ID
            // Unique prevents the same payment being used twice
            $table->string('payment_intent_id')->unique();

            // Stripe amount in smallest currency unit
            // Example: AED 100 = 10000
            $table->unsignedBigInteger('amount');

            $table->string('currency', 10);

            // Example: succeeded
            $table->string('status', 50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};