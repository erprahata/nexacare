<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Poliklinik
        $poliUmum = Clinic::create(['name' => 'Poli Umum', 'is_executive' => false]);
        $poliDalamVIP = Clinic::create(['name' => 'Poli Penyakit Dalam (VIP)', 'is_executive' => true]);

        // 2. Buat Data Staf & Tenaga Medis
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@nexacare.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $dokter = User::create([
            'clinic_id' => $poliUmum->id,
            'name' => 'dr. Andi Pratama',
            'email' => 'dokter@nexacare.com',
            'password' => Hash::make('password123'),
            'role' => 'doctor',
        ]);

        User::create([
            'name' => 'Budi Apoteker, S.Farm',
            'email' => 'apotek@nexacare.com',
            'password' => Hash::make('password123'),
            'role' => 'pharmacist',
        ]);

        // 3. Buat Master Obat (Harga Realistis)
        $paracetamol = Medicine::create(['sku_code' => 'MED-001', 'name' => 'Paracetamol 500mg', 'stock' => 150, 'price' => 5000]);
        $amoxicillin = Medicine::create(['sku_code' => 'MED-002', 'name' => 'Amoxicillin 500mg', 'stock' => 100, 'price' => 12000]);
        $omeprazole = Medicine::create(['sku_code' => 'MED-003', 'name' => 'Omeprazole 20mg', 'stock' => 80, 'price' => 15000]);

        // 4. Buat Data Pasien
        $pasienReguler = Patient::create([
            'medical_record_number' => 'RM-2026-08001',
            'name' => 'Siti Aminah',
            'dob' => '1990-05-15',
            'gender' => 'P',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10',
            'is_vvip' => false,
        ]);

        $pasienVvip = Patient::create([
            'medical_record_number' => 'RM-2026-08002',
            'name' => 'Ahmad Sudirman (Pejabat)',
            'dob' => '1975-11-20',
            'gender' => 'L',
            'phone' => '081999888777',
            'address' => 'Perumahan Elite Blok A1',
            'is_vvip' => true,
        ]);

        // 5. Simulasi Alur Pasien 1 (Selesai Pemeriksaan & Tagihan)
        $appointment = Appointment::create([
            'patient_id' => $pasienReguler->id,
            'doctor_id' => $dokter->id,
            'clinic_id' => $poliUmum->id,
            'estimated_time' => Carbon::now()->addMinutes(10),
            'priority_level' => 'regular',
            'status' => 'completed',
        ]);

        // 6. Buat Rekam Medis untuk Pasien 1
        $emr = MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'pre_triage_notes' => ['tensi' => '120/80', 'suhu' => '38.5', 'keluhan' => 'Demam tinggi 2 hari dan sakit tenggorokan'],
            'diagnosis' => 'Faringitis Akut',
            'doctor_notes' => 'Istirahat cukup, hindari makanan berminyak.',
            'is_locked' => true,
        ]);

        // 7. Buat Resep Obat (E-Prescription)
        $resep = Prescription::create([
            'medical_record_id' => $emr->id,
            'status' => 'pending',
        ]);

        PrescriptionItem::create(['prescription_id' => $resep->id, 'medicine_id' => $paracetamol->id, 'quantity' => 10, 'dosage_instructions' => '3x sehari 1 tablet sesudah makan']);
        PrescriptionItem::create(['prescription_id' => $resep->id, 'medicine_id' => $amoxicillin->id, 'quantity' => 15, 'dosage_instructions' => '3x sehari 1 tablet (habiskan)']);

        // 8. Buat Tagihan Kasir
        $tagihan = Invoice::create([
            'appointment_id' => $appointment->id,
            'total_amount' => 175000 + (10 * 5000) + (15 * 12000), // Jasa + Obat
            'payment_status' => 'unpaid',
        ]);

        // 9. TAMBAHAN: Pasien Baru untuk Uji Coba EMR Dokter
        $pasienBaru = Patient::create([
            'medical_record_number' => 'RM-2026-08003',
            'name' => 'Budi Santoso',
            'dob' => '1988-12-10',
            'gender' => 'L',
            'phone' => '081122334455',
            'address' => 'Jl. Sudirman No. 45',
            'is_vvip' => false,
        ]);

        // Buat antrean baru yang belum diperiksa (status: arrived)
        Appointment::create([
            'patient_id' => $pasienBaru->id,
            'doctor_id' => $dokter->id,
            'clinic_id' => $poliUmum->id,
            'estimated_time' => Carbon::now()->addMinutes(20),
            'priority_level' => 'regular',
            'status' => 'arrived', // Status ini akan memunculkan tombol "Periksa Pasien"
        ]);

        // Akun Kasir / Bagian Keuangan
        \App\Models\User::factory()->create([
            'name' => 'Nisa Kasir',
            'email' => 'kasir@nexacare.com',
            'password' => bcrypt('password123'),
            'role' => 'cashier', // Pastikan penamaan role ini persis dengan ENUM di migration tabel users Anda
        ]);

        InvoiceItem::create(['invoice_id' => $tagihan->id, 'item_type' => 'consultation_fee', 'description' => 'Jasa Konsultasi dr. Andi Pratama', 'amount' => 175000]);
        InvoiceItem::create(['invoice_id' => $tagihan->id, 'item_type' => 'medicine', 'description' => 'Paracetamol 500mg (10x)', 'amount' => 50000]);
        InvoiceItem::create(['invoice_id' => $tagihan->id, 'item_type' => 'medicine', 'description' => 'Amoxicillin 500mg (15x)', 'amount' => 180000]);
    }
}