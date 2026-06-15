<?php

use App\Models\Party;
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
        Schema::table('challan_items', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->constrained();
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->dateTime('challan_date')->default(now());
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignIdFor(Party::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challan_items', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['warehouse_id']);
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn(['challan_date']);
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['party_id']);
            $table->dropColumn(['party_id']);
        });
    }
};
