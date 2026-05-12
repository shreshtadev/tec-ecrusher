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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->foreignIdFor(Warehouse::class)->constrained('warehouses');
            $table->decimal('quantity_change', 12, 2);
            $table->enum('adjustment_type', ['Damage', 'Loss', 'Correction', 'Audit', 'Other'])->default('Other');
            $table->text('reason');
            $table->string('reference_number')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
