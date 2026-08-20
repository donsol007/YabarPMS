<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 20)->unique();

            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name');
            $table->enum('sex', ['Male', 'Female']);
            $table->date('date_of_birth');
            $table->string('mobile_number', 20);
            $table->string('mother_maiden_name')->nullable();
            $table->text('residential_address')->nullable();
            $table->foreignId('state_of_origin_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('lga_id')->nullable()->constrained('lgas')->nullOnDelete();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();
            $table->enum('religion', ['Christianity', 'Islam', 'Traditional', 'Other'])->nullable();
            $table->string('email')->unique();

            $table->decimal('amount_to_invest', 15, 2)->default(0);

            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('account_name')->nullable();
            $table->string('account_number', 20)->nullable();
            $table->enum('account_type', ['Savings', 'Current', 'Domiciliary'])->nullable();
            $table->string('bvn', 11)->nullable();
            $table->date('account_opening_date')->nullable();
            $table->text('bank_address')->nullable();

            $table->string('occupation')->nullable();
            $table->string('employer_name')->nullable();
            $table->text('employer_address')->nullable();

            $table->text('hobbies')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};