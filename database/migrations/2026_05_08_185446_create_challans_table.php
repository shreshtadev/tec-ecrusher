<?php

use App\Models\Driver;
use App\Models\Item;
use App\Models\Party;
use App\Models\Vehicle;
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
        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_number', 50)->unique();
            $table->foreignIdFor(Party::class)->constrained('parties')->nullable();
            $table->foreignIdFor(Vehicle::class)->constrained('vehicles');
            $table->foreignIdFor(Driver::class)->constrained('drivers')->nullable();
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->nullOnDelete();
            $table->decimal('quantity_cft', 10, 2);
            $table->string('payment_mode', 20)->default('A/C');
            $table->enum('status', ['Pending', 'Invoiced'])->default('Pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challans');
    }
};
