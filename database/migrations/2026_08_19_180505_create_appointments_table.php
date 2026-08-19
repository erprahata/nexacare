<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary(); // QR Code ID
            
            // Relasi ke pasien (UUID)
            $table->foreignUuid('patient_id')->constrained('patients')->cascadeOnDelete();
            
            // Relasi ke dokter (BigInt)
            $table->foreignId('doctor_id')->constrained('users');
            
            // Relasi ke poli (BigInt)
            $table->foreignId('clinic_id')->constrained('clinics');
            
            $table->timestamp('estimated_time')->nullable(); // ETA dinamis
            $table->enum('priority_level', ['regular', 'vip', 'emergency'])->default('regular');
            $table->enum('status', ['waiting', 'arrived', 'in_progress', 'completed', 'delayed'])->default('waiting');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
