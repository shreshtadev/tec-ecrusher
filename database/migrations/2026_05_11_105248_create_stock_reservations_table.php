<?php

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Models\Challan;
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
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Challan::class)->unique()->constrained('challans')->cascadeOnDelete();
            $table->foreignIdFor(Warehouse::class)->constrained('warehouses');
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->decimal('quantity_reserved', 12, 2);
            $table->enum('status', ['reserved', 'finalized', 'cancelled'])->default('reserved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};
