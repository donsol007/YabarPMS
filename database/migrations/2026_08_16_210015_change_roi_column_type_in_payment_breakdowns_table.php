<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_breakdowns', function (Blueprint $table) {
            $table->string('roi', 50)->default('')->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_breakdowns', function (Blueprint $table) {
            $table->decimal('roi', 8, 2)->default(0)->change();
        });
    }
};