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
        Schema::create('challan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challan_id')->constrained('challans')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');

            // Data Fields
            $table->decimal('quantity_cft', 10, 2)->default(0);
            $table->decimal('rate_at_sale', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challan_items');
    }
};
