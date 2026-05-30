<?php

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
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedInteger('invoice_sequence')
                ->default(1)->after('challan_prefix');

            $table->unsignedInteger('challan_sequence')
                ->default(1)->after('invoice_sequence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('invoice_sequence');
            $table->dropColumn('challan_sequence');
        });
    }
};
