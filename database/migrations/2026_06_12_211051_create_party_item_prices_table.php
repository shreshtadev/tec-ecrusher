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
        Schema::create('party_item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('price_per_unit', 10, 2);

            $table->timestamps();

            $table->unique(['party_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_item_prices');
    }
};
