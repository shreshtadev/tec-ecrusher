<?php

use App\Domains\Master\Models\Party;
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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique();
            $table->date('voucher_date');
            $table->foreignIdFor(Party::class)->constrained('parties');
            $table->enum('voucher_type', ['Payment', 'Receipt']);
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode')->default('Cash');
            $table->text('remarks')->nullable();
            $table->foreignIdFor(Invoice::class, 'reference_invoice_id')->nullable()->constrained('invoices');
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
