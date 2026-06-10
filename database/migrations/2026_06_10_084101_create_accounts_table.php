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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Main Checking", "Petty Cash"
            $table->string('account_number')->unique()->nullable();
            $table->string('account_type')->default('asset'); // e.g., asset, liability, equity
            $table->string('bank_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->enum('account_mode', ['cash', 'bank', 'ledger'])->default('cash'); // Optional: to specify the mode of transaction
            $table->decimal('balance', 15, 2)->default(0.00); // Optional: tracking current balance
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
