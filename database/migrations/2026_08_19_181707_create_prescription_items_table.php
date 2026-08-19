<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignId('medicine_id')->constrained('medicines');
            $table->integer('quantity');
            $table->string('dosage_instructions'); // Contoh: 3x sehari sesudah makan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
