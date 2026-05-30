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
            $table->string('voucher_prefix', 20)->nullable()->default('VCH')->after('invoice_prefix');
            $table->string('voucher_number_format', 50)->nullable()->after('invoice_number_format')->nullable();
            $table->unsignedInteger('voucher_sequence')->nullable()->after('invoice_sequence')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('voucher_prefix');
            $table->dropColumn('voucher_number_format');
            $table->dropColumn('voucher_sequence');
        });
    }
};
