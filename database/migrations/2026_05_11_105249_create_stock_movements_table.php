<?php

use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Warehouse;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->foreignIdFor(Warehouse::class)->constrained('warehouses');
            $table->foreignIdFor(Challan::class)->nullable()->constrained('challans')->nullOnDelete();
            $table->foreignIdFor(Invoice::class)->nullable()->constrained('invoices')->nullOnDelete();
            $table->unsignedBigInteger('adjustment_id')->nullable();
            $table->enum('movement_type', ['IN', 'OUT', 'RESERVE', 'UNRESERVE', 'ADJUSTMENT'])->default('IN');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('adjustment_id')->references('id')->on('stock_adjustments')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
