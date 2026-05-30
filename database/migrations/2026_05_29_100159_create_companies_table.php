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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            // Business Information
            $table->string('name', 150);
            $table->string('legal_name', 200)->nullable();

            $table->string('gstin', 15)->nullable();
            $table->string('pan', 10)->nullable();

            $table->text('address')->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 150)->nullable();

            $table->string('state', 100)->nullable();
            $table->string('state_code', 2)->nullable(); // GST state code

            $table->string('cin', 21)->nullable(); // Companies Act

            $table->string('upi_id', 100)->nullable();

            $table->string('invoice_number_format', 50)->nullable();
            $table->string('challan_number_format', 50)->nullable();

            // Branding
            $table->string('logo', 255)->nullable();

            // Bank Details
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('ifsc', 11)->nullable();
            $table->string('branch', 100)->nullable();

            // Invoice Configuration
            $table->string('invoice_prefix', 20)->default('INV');
            $table->string('challan_prefix', 20)->default('CHL');

            $table->string('authorized_signatory', 100)->nullable();

            // Invoice Text
            $table->text('invoice_terms')->nullable();
            $table->text('invoice_declaration')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
