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
        Schema::table('companies', function (Blueprint $table) {
            $table->date('challan_last_reset_at')->nullable();
            $table->date('invoice_last_reset_at')->nullable();
            $table->date('voucher_last_reset_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('challan_last_reset_at');
            $table->dropColumn('invoice_last_reset_at');
            $table->dropColumn('voucher_last_reset_at');
        });
    }
};
