<?php

use App\Models\Item;
use App\Models\Warehouse;
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
        Schema::create('production_entries', function (Blueprint $table) {
            $table->id();
            $table->date('production_entry_date');
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->foreignIdFor(Warehouse::class)->constrained('warehouses');
            $table->decimal('quantity', 15, 2);
            $table->string('batch_no', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_entries');
    }
};
