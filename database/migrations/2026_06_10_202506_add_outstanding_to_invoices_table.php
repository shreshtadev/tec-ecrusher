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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('outstanding_amount', 15, 2)
                ->default(0)
                ->after('total_amount');

            $table->string('payment_status')
                ->default('unpaid')
                ->after('outstanding_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('outstanding_amount');
            $table->dropColumn('payment_status');
        });
    }
};
