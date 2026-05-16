<?php

use App\Domains\Master\Models\Driver;
use App\Domains\Master\Models\Item;
use App\Domains\Master\Models\Party;
use App\Domains\Master\Models\Vehicle;
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
        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_number', 50)->unique();
            $table->foreignIdFor(Party::class)->constrained('parties');
            $table->foreignIdFor(Vehicle::class)->constrained('vehicles');
            $table->foreignIdFor(Driver::class)->constrained('drivers');
            $table->foreignIdFor(Item::class)->constrained('items');
            $table->foreignIdFor(Invoice::class)
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
