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
        Schema::table('challans', function (Blueprint $table) {
            $table->decimal('rate_at_sale', 10, 2)->default(0.00)->after('quantity_cft');
            $table->decimal('amount', 12, 2)->default(0.00)->after('rate_at_sale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn('rate_at_sale');
            $table->dropColumn('amount');
        });
    }
};
