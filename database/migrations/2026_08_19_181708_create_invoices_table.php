<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->string('payment_method')->nullable(); // Cash, QRIS, Transfer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
