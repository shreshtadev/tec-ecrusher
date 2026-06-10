<?php

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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Party::class)->constrained()->onDelete('cascade')->nullable();
            $table->string('vehicle_number', 50)->unique();
            $table->decimal('capacity_cft', 8, 2)->nullable();
            $table->string('unit', 5)->default('CFT');
            $table->string('vehicle_type', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
