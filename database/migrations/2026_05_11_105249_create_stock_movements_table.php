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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class)
                ->constrained('items');

            $table->foreignIdFor(Warehouse::class)
                ->constrained('warehouses');

            $table->enum('movement_type', [
                'IN',
                'OUT',
                'ADJUSTMENT',
            ]);

            $table->decimal('quantity', 12, 2);

            $table->decimal('unit_cost', 12, 2)
                ->nullable();

            // polymorphic source
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');

            $table->timestamp('movement_date');

            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'source_type',
                'source_id',
            ]);
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
