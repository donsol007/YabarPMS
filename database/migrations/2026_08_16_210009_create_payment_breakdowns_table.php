<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_debt_instrument_id')->constrained()->cascadeOnDelete();
            $table->string('roi', 50)->default('');
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('payment_date');
            $table->enum('status', ['paid', 'unpaid', 'blank'])->default('blank');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_breakdowns');
    }
};