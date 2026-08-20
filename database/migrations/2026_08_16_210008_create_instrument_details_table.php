<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instrument_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_debt_instrument_id')->constrained()->cascadeOnDelete()->unique();
            $table->date('subscription_date')->nullable();
            $table->string('instrument')->default('CP');
            $table->string('tenor')->nullable();
            $table->decimal('rental_rate', 8, 2)->nullable();
            $table->date('settlement_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instrument_details');
    }
};