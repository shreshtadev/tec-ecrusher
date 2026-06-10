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
            // Drop foreign keys first if you defined them as constrained
            $table->dropForeign(['item_id']);

            // Drop the columns
            $table->dropColumn(['item_id', 'quantity_cft', 'rate_at_sale', 'amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            //
        });
    }
};
