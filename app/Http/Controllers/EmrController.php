<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Inertia\Inertia;

class EmrController extends Controller
{
    public function create($appointment_id)
    {
        // Tarik data janji temu beserta relasi pasiennya
        $appointment = Appointment::with('patient')->findOrFail($appointment_id);
        
        // Tarik master data obat untuk form resep
        $medicines = Medicine::where('stock', '>', 0)->get();

        return Inertia::render('Doctor/EmrForm', [
            'appointment' => $appointment,
            'medicines' => $medicines
        ]);
    }

    public function store(Request $request, $appointment_id)
    {
        // 1. Validasi Input dari Frontend
        $request->validate([
            'diagnosis' => 'required|string',
            'doctor_notes' => 'nullable|string',
            'prescriptions' => 'array',
            'prescriptions.*.medicine_id' => 'nullable|exists:medicines,id',
            'prescriptions.*.quantity' => 'nullable|integer|min:1',
        ]);

        // 2. Mulai Proteksi Database (Mencegah Race Condition / Partial Data)
        DB::beginTransaction();

        try {
            $appointment = Appointment::findOrFail($appointment_id);

            // 3. Simpan Hasil Diagnosis Utama
            $emr = MedicalRecord::create([
                'appointment_id' => $appointment->id,
                'diagnosis' => $request->diagnosis,
                'doctor_notes' => $request->doctor_notes,
                'is_locked' => true,
            ]);

            // 4. Siapkan Lembar Tagihan (Invoice)
            $invoice = Invoice::create([
                'appointment_id' => $appointment->id,
                'total_amount' => 0,
                'payment_status' => 'unpaid',
            ]);

            $totalAmount = 0;

            // Tambahkan Biaya Jasa Dokter ke Tagihan
            $consultationFee = 150000;
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_type' => 'consultation_fee',
                'description' => 'Jasa Konsultasi Pemeriksaan Dokter',
                'amount' => $consultationFee,
            ]);
            $totalAmount += $consultationFee;

            // 5. Eksekusi Resep Obat & Potong Stok
            if (!empty($request->prescriptions) && !empty($request->prescriptions[0]['medicine_id'])) {
                
                $prescription = Prescription::create([
                    'medical_record_id' => $emr->id,
                    'status' => 'pending', // Dikirim ke antrean Apoteker
                ]);

                foreach ($request->prescriptions as $item) {
                    if (empty($item['medicine_id'])) continue;

                    // Mengunci baris obat agar tidak ada yang beli bersamaan
                    $medicine = Medicine::lockForUpdate()->findOrFail($item['medicine_id']);
                    
                    if ($medicine->stock < $item['quantity']) {
                        throw new \Exception("Stok obat {$medicine->name} tidak mencukupi.");
                    }

                    // Pemotongan Stok
                    $medicine->decrement('stock', $item['quantity']);

                    // Catat ke Resep
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_id' => $medicine->id,
                        'quantity' => $item['quantity'],
                        'dosage_instructions' => $item['dosage_instructions'],
                    ]);

                    // Tambahkan harga obat ke Tagihan
                    $medicinePrice = $medicine->price * $item['quantity'];
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => 'medicine',
                        'description' => "Resep: {$medicine->name} ({$item['quantity']}x)",
                        'amount' => $medicinePrice,
                    ]);
                    $totalAmount += $medicinePrice;
                }
            }

            // 6. Finalisasi Total Harga dan Tutup Antrean Pasien
            $invoice->update(['total_amount' => $totalAmount]);
            $appointment->update(['status' => 'completed']);

            // 7. Commit semua perubahan ke Database
            DB::commit();

            // 8. Redirect otomatis ke Dashboard
            return redirect()->route('dashboard')->with('success', 'Rekam Medis berhasil disimpan!');

        } catch (\Exception $e) {
            // Jika ada gagal di tengah jalan, batalkan semuanya
            DB::rollBack(); 
            
            // Catat error ke file log
            \Illuminate\Support\Facades\Log::error("EMR ERROR: " . $e->getMessage()); 
            
            // Tampilkan error di UI Glassmorphism kita
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}