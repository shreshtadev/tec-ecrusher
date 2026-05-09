<?php

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
        Schema::table('challans', function (Blueprint $table) {
            $table->foreignIdFor(Invoice::class)
                ->nullable()
                ->after('item_id')
                ->constrained('invoices')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challans', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Invoice::class);
        });
    }
};
