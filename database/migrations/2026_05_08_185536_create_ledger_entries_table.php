<?php

use App\Domains\Master\Models\Party;
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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->date('entry_date');
            $table->foreignIdFor(Party::class)->constrained('parties');

            // Morphic relationship to link to Invoices, Vouchers, or Bata Expenses
            $table->morphs('recordable');

            $table->string('description'); // e.g., "Payment received for Inv #102" [cite: 138]
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);

            // Tracking the running balance for the Party Ledger report [cite: 138]
            $table->decimal('balance', 12, 2);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
