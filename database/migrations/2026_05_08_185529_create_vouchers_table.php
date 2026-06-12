<?php

use App\Models\Invoice;
use App\Models\Party;
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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no', 50)->unique();
            $table->date('voucher_date');
            $table->foreignIdFor(Party::class)->constrained('parties');
            $table->enum('voucher_type', ['payment', 'receipt', 'credit_note', 'debit_note', 'journal']);
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode', 20)->default('Cash');
            $table->text('remarks')->nullable();
            $table->foreignIdFor(Invoice::class, 'invoice_id')->nullable()->constrained('invoices');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
