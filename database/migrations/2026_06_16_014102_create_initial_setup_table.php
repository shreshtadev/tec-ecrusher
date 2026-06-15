<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('account_number')->nullable()->unique();
            $table->string('account_type')->default('asset');
            $table->string('bank_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->enum('account_mode', ['cash', 'bank', 'ledger'])->default('cash');
            $table->decimal('balance', 15)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unsignedBigInteger('party_id')->index('accounts_party_id_foreign')->nullable();

            $table->unique(['title', 'party_id']);
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('log_name')->nullable()->index();
            $table->text('description');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('event')->nullable();
            $table->string('causer_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->json('attribute_changes')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['causer_type', 'causer_id'], 'causer');
            $table->index(['subject_type', 'subject_id'], 'subject');
        });

        Schema::create('challan_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('challan_id')->index('challan_items_challan_id_foreign');
            $table->unsignedBigInteger('item_id')->index('challan_items_item_id_foreign');
            $table->decimal('quantity_cft', 10)->default(0);
            $table->decimal('rate_at_sale', 10)->default(0);
            $table->decimal('amount', 12)->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('warehouse_id');
        });

        Schema::create('challans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('challan_number', 50)->unique();
            $table->unsignedBigInteger('party_id')->index('challans_party_id_foreign');
            $table->unsignedBigInteger('vehicle_id')->index('challans_vehicle_id_foreign');
            $table->unsignedBigInteger('driver_id')->index('challans_driver_id_foreign');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('challans_invoice_id_foreign');
            $table->string('payment_mode', 20)->default('A/C');
            $table->enum('status', ['Pending', 'Invoiced'])->default('Pending');
            $table->decimal('driver_bata', 10)->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('company_id')->nullable()->index('challans_company_id_foreign');
            $table->dateTime('challan_date')->default('2026-06-15 21:22:19');
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 150);
            $table->string('legal_name', 200)->nullable();
            $table->string('gstin', 15)->nullable();
            $table->string('pan', 10)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 150)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('state_code', 2)->nullable();
            $table->string('cin', 21)->nullable();
            $table->string('upi_id', 100)->nullable();
            $table->string('invoice_number_format', 50)->nullable();
            $table->string('voucher_number_format', 50)->nullable();
            $table->string('challan_number_format', 50)->nullable();
            $table->string('logo')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('ifsc', 11)->nullable();
            $table->string('branch', 100)->nullable();
            $table->string('invoice_prefix', 5)->default('INV');
            $table->string('voucher_prefix', 5)->nullable()->default('VCH');
            $table->string('challan_prefix', 5)->default('CHL');
            $table->string('party_account_prefix', 5)->default('PAC');
            $table->string('company_account_prefix', 5)->default('CAC');
            $table->unsignedInteger('invoice_sequence')->default(0);
            $table->unsignedInteger('voucher_sequence')->nullable()->default(0);
            $table->unsignedInteger('challan_sequence')->default(0);
            $table->unsignedInteger('party_account_sequence')->default(0);
            $table->unsignedInteger('company_account_sequence')->default(0);
            $table->string('authorized_signatory', 100)->nullable();
            $table->text('invoice_terms')->nullable();
            $table->text('invoice_declaration')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->timestamps();
            $table->date('challan_last_reset_at')->nullable();
            $table->date('invoice_last_reset_at')->nullable();
            $table->date('voucher_last_reset_at')->nullable();
            $table->date('party_account_last_reset_at')->nullable();
            $table->date('company_account_last_reset_at')->nullable();
        });

        Schema::create('drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('party_id')->index('drivers_party_id_foreign')->nullable();
            $table->string('full_name', 120);
            $table->string('phone_number', 20);
            $table->timestamps();
            $table->unique(['party_id', 'full_name', 'phone_number']);
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('expenditure_date');
            $table->string('category', 20);
            $table->unsignedBigInteger('party_id')->index('expenses_party_id_foreign');
            $table->decimal('amount', 15);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('voucher_id')->nullable()->index('expenses_voucher_id_foreign');
            $table->unsignedBigInteger('invoice_id')->nullable()->index('expenses_invoice_id_foreign');
        });

        Schema::create('invoice_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('voucher_id')->index('invoice_allocations_voucher_id_foreign');
            $table->decimal('allocated_amount', 15);
            $table->timestamps();

            $table->unique(['invoice_id', 'voucher_id']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->index('invoice_items_invoice_id_foreign');
            $table->unsignedBigInteger('item_id')->index('invoice_items_item_id_foreign');
            $table->decimal('quantity', 10)->default(0);
            $table->decimal('rate_at_sale', 10)->default(0);
            $table->decimal('amount', 12)->default(0);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number', 50)->unique();
            $table->unsignedBigInteger('party_id')->index('invoices_party_id_foreign');
            $table->decimal('total_amount', 12);
            $table->decimal('outstanding_amount', 15)->default(0);
            $table->string('payment_status')->default('unpaid');
            $table->decimal('driver_bata', 10)->default(0);
            $table->string('payment_mode', 20)->default('Credit');
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('company_id')->nullable()->index('invoices_company_id_foreign');
            $table->decimal('discount_amount', 12)->default(0);
            $table->decimal('grand_total', 12)->default(0);
            $table->dateTime('invoice_date')->default('2026-06-15 21:22:19');
        });

        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('material_name', 100);
            $table->decimal('price_per_unit', 10);
            $table->string('unit', 5)->default('CFT');
            $table->timestamps();
        });

        Schema::create('parties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('full_name', 120);
            $table->string('address_line_1', 150)->nullable();
            $table->string('address_line_2', 150)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 5)->default('KA');
            $table->string('postal_code', 12)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->enum('party_type', ['Customer', 'Supplier', 'Employee', 'Other'])->default('Customer');
            $table->timestamps();
        });

        Schema::create('party_item_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('party_id');
            $table->unsignedBigInteger('item_id')->index('party_item_prices_item_id_foreign');
            $table->decimal('price_per_unit', 10);
            $table->timestamps();

            $table->unique(['party_id', 'item_id']);
        });

        Schema::create('production_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('production_entry_date');
            $table->unsignedBigInteger('item_id')->index('production_entries_item_id_foreign');
            $table->unsignedBigInteger('warehouse_id')->index('production_entries_warehouse_id_foreign');
            $table->decimal('quantity', 15);
            $table->string('batch_no', 50)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('stock_issue_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_issue_id')->index('stock_issue_items_stock_issue_id_foreign');
            $table->unsignedBigInteger('item_id')->index('stock_issue_items_item_id_foreign');
            $table->decimal('quantity', 12, 3);
            $table->timestamps();
        });

        Schema::create('stock_issues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_id')->index('stock_issues_warehouse_id_foreign');
            $table->string('issue_no')->unique();
            $table->date('issue_date');
            $table->string('purpose');
            $table->text('remarks')->nullable();
            $table->enum('status', ['draft', 'issued', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('company_id')->index('stock_issues_company_id_foreign');
            $table->timestamps();
        });

        Schema::create('stock_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('warehouse_id')->index('stock_levels_warehouse_id_foreign');
            $table->decimal('available_qty', 12)->default(0);
            $table->decimal('reserved_qty', 12)->default(0);
            $table->timestamps();

            $table->unique(['item_id', 'warehouse_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id')->index('stock_movements_item_id_foreign');
            $table->unsignedBigInteger('warehouse_id')->index('stock_movements_warehouse_id_foreign');
            $table->enum('movement_type', ['IN', 'OUT', 'ADJUSTMENT']);
            $table->decimal('quantity', 12);
            $table->decimal('unit_cost', 12)->nullable();
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->timestamp('movement_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['source_type', 'source_id']);
        });

        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id')->index('stock_reservations_item_id_foreign');
            $table->unsignedBigInteger('warehouse_id')->index('stock_reservations_warehouse_id_foreign');
            $table->decimal('quantity', 12);
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->enum('status', ['reserved', 'finalized', 'cancelled'])->default('reserved');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('party_id')->index('vehicles_party_id_foreign');
            $table->string('vehicle_number', 50)->unique();
            $table->decimal('capacity_cft')->nullable();
            $table->string('unit', 5)->default('CFT');
            $table->string('vehicle_type', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('voucher_no', 50)->unique();
            $table->date('voucher_date');
            $table->unsignedBigInteger('party_id')->index('vouchers_party_id_foreign');
            $table->enum('voucher_type', ['payment', 'receipt', 'credit_note', 'debit_note', 'journal']);
            $table->string('reference_no', 50)->nullable();
            $table->decimal('amount', 12);
            $table->string('payment_mode', 20)->default('Cash');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable()->index('vouchers_invoice_id_foreign');
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('company_id')->nullable()->index('vouchers_company_id_foreign');
            $table->unsignedBigInteger('from_account_id')->nullable()->index('vouchers_from_account_id_foreign');
            $table->unsignedBigInteger('to_account_id')->nullable()->index('vouchers_to_account_id_foreign');
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('code', 20)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('challan_items', function (Blueprint $table) {
            $table->foreign(['challan_id'])->references(['id'])->on('challans')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->foreign(['company_id'])->references(['id'])->on('companies')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['driver_id'])->references(['id'])->on('drivers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['invoice_id'])->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['vehicle_id'])->references(['id'])->on('vehicles')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('cascade');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign(['invoice_id'])->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['voucher_id'])->references(['id'])->on('vouchers')->onUpdate('restrict')->onDelete('set null');
        });

        Schema::table('invoice_allocations', function (Blueprint $table) {
            $table->foreign(['invoice_id'])->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['voucher_id'])->references(['id'])->on('vouchers')->onUpdate('restrict')->onDelete('cascade');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreign(['invoice_id'])->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreign(['company_id'])->references(['id'])->on('companies')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('party_item_prices', function (Blueprint $table) {
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('cascade');
        });

        Schema::table('production_entries', function (Blueprint $table) {
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('restrict')->onDelete('cascade');
        });

        Schema::table('stock_issue_items', function (Blueprint $table) {
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['stock_issue_id'])->references(['id'])->on('stock_issues')->onUpdate('restrict')->onDelete('cascade');
        });

        Schema::table('stock_issues', function (Blueprint $table) {
            $table->foreign(['company_id'])->references(['id'])->on('companies')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('stock_levels', function (Blueprint $table) {
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['warehouse_id'])->references(['id'])->on('warehouses')->onUpdate('restrict')->onDelete('restrict');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('cascade');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreign(['company_id'])->references(['id'])->on('companies')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['from_account_id'])->references(['id'])->on('accounts')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['invoice_id'])->references(['id'])->on('invoices')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['party_id'])->references(['id'])->on('parties')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['to_account_id'])->references(['id'])->on('accounts')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign('vouchers_company_id_foreign');
            $table->dropForeign('vouchers_from_account_id_foreign');
            $table->dropForeign('vouchers_invoice_id_foreign');
            $table->dropForeign('vouchers_party_id_foreign');
            $table->dropForeign('vouchers_to_account_id_foreign');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign('vehicles_party_id_foreign');
        });

        Schema::table('stock_reservations', function (Blueprint $table) {
            $table->dropForeign('stock_reservations_item_id_foreign');
            $table->dropForeign('stock_reservations_warehouse_id_foreign');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign('stock_movements_item_id_foreign');
            $table->dropForeign('stock_movements_warehouse_id_foreign');
        });

        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropForeign('stock_levels_item_id_foreign');
            $table->dropForeign('stock_levels_warehouse_id_foreign');
        });

        Schema::table('stock_issues', function (Blueprint $table) {
            $table->dropForeign('stock_issues_company_id_foreign');
            $table->dropForeign('stock_issues_warehouse_id_foreign');
        });

        Schema::table('stock_issue_items', function (Blueprint $table) {
            $table->dropForeign('stock_issue_items_item_id_foreign');
            $table->dropForeign('stock_issue_items_stock_issue_id_foreign');
        });

        Schema::table('production_entries', function (Blueprint $table) {
            $table->dropForeign('production_entries_item_id_foreign');
            $table->dropForeign('production_entries_warehouse_id_foreign');
        });

        Schema::table('party_item_prices', function (Blueprint $table) {
            $table->dropForeign('party_item_prices_item_id_foreign');
            $table->dropForeign('party_item_prices_party_id_foreign');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_company_id_foreign');
            $table->dropForeign('invoices_party_id_foreign');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign('invoice_items_invoice_id_foreign');
            $table->dropForeign('invoice_items_item_id_foreign');
        });

        Schema::table('invoice_allocations', function (Blueprint $table) {
            $table->dropForeign('invoice_allocations_invoice_id_foreign');
            $table->dropForeign('invoice_allocations_voucher_id_foreign');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_invoice_id_foreign');
            $table->dropForeign('expenses_party_id_foreign');
            $table->dropForeign('expenses_voucher_id_foreign');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign('drivers_party_id_foreign');
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->dropForeign('challans_company_id_foreign');
            $table->dropForeign('challans_driver_id_foreign');
            $table->dropForeign('challans_invoice_id_foreign');
            $table->dropForeign('challans_party_id_foreign');
            $table->dropForeign('challans_vehicle_id_foreign');
        });

        Schema::table('challan_items', function (Blueprint $table) {
            $table->dropForeign('challan_items_challan_id_foreign');
            $table->dropForeign('challan_items_item_id_foreign');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign('accounts_party_id_foreign');
        });

        Schema::dropIfExists('warehouses');

        Schema::dropIfExists('vouchers');

        Schema::dropIfExists('vehicles');

        Schema::dropIfExists('stock_reservations');

        Schema::dropIfExists('stock_movements');

        Schema::dropIfExists('stock_levels');

        Schema::dropIfExists('stock_issues');

        Schema::dropIfExists('stock_issue_items');

        Schema::dropIfExists('production_entries');

        Schema::dropIfExists('party_item_prices');

        Schema::dropIfExists('parties');

        Schema::dropIfExists('items');

        Schema::dropIfExists('invoices');

        Schema::dropIfExists('invoice_items');

        Schema::dropIfExists('invoice_allocations');

        Schema::dropIfExists('expenses');

        Schema::dropIfExists('drivers');

        Schema::dropIfExists('companies');

        Schema::dropIfExists('challans');

        Schema::dropIfExists('challan_items');

        Schema::dropIfExists('activity_log');

        Schema::dropIfExists('accounts');
    }
};
