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
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class)
                ->constrained('items');

            $table->foreignIdFor(Warehouse::class)
                ->constrained('warehouses');

            $table->decimal('quantity', 12, 2);

            // polymorphic source
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');

            $table->enum('status', [
                'reserved',
                'finalized',
                'cancelled',
            ])->default('reserved');

            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

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
        Schema::dropIfExists('stock_reservations');
    }
};
