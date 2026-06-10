<?php

use App\Models\Account;
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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignIdFor(Account::class, 'from_account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Account::class, 'to_account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_no', 50)->nullable()->after('voucher_type'); // Optional: for external reference numbers

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['from_account_id']);
            $table->dropForeign(['to_account_id']);
            $table->dropColumn(['from_account_id', 'to_account_id']);
            $table->dropColumn('reference_no');
        });
    }
};
