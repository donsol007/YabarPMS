<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('portfolio_access_code')->nullable()->after('hobbies');
            $table->string('portfolio_access_token', 64)->nullable()->unique()->after('portfolio_access_code');
            $table->timestamp('portfolio_access_code_set_at')->nullable()->after('portfolio_access_token');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'portfolio_access_code',
                'portfolio_access_token',
                'portfolio_access_code_set_at',
            ]);
        });
    }
};
