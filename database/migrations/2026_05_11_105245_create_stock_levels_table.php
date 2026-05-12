<?php

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
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
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->foreignIdFor(Warehouse::class)->constrained('warehouses');
            $table->decimal('available_qty', 12, 2)->default(0);
            $table->decimal('reserved_qty', 12, 2)->default(0);
            $table->enum('valuation_method', ['FIFO', 'LIFO'])->default('FIFO');
            $table->unique(['item_id', 'warehouse_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
