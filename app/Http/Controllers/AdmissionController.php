<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AdmissionController extends Controller
{
    // 1. Tampilkan Halaman Form Pendaftaran
    public function create()
    {
        // Tarik data poli dan dokter untuk opsi dropdown di frontend
        $clinics = Clinic::all();
        $doctors = User::where('role', 'doctor')->get();

        return Inertia::render('Frontdesk/Register', [
            'clinics' => $clinics,
            'doctors' => $doctors
        ]);
    }

    // 2. Proses Pendaftaran Pasien & Masukkan ke Antrean
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|digits:16', 
            'dob' => 'required|date',
            'phone' => 'nullable|string|max:20', 
            'address' => 'required|string',
            'allergies' => 'nullable|string', // <-- DITAMBAHKAN
            'clinic_id' => 'required|exists:clinics,id',
            'doctor_id' => 'required|exists:users,id',
            'is_vvip' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            // A. Buat Nomor RM Otomatis (Format: RM-TAHUN-BULAN + URUTAN 3 DIGIT)
            // Contoh: RM-2026-08005
            $tahun = date('Y');
            $bulan = date('m'); // Mendapatkan bulan saat ini (contoh: 08)
            $totalPasien = Patient::count();
            
            // Format 3 digit urutan (contoh: 005)
            $urutan = str_pad($totalPasien + 1, 3, '0', STR_PAD_LEFT); 
            
            // Menggabungkan semuanya menjadi RM-2026-08005
            $nomorRM = 'RM-' . $tahun . '-' . $bulan . $urutan;

            // B. Simpan Data Pasien Baru
            $patient = Patient::create([
                'medical_record_number' => $nomorRM,
                'name' => $request->name,
                'nik' => $request->nik,
                'dob' => $request->dob,
                'phone' => $request->phone, 
                'address' => $request->address,
                'allergies' => $request->allergies, // <-- DITAMBAHKAN
                'is_vvip' => $request->is_vvip ?? false,
            ]);

            // C. Masukkan Pasien ke Tabel Antrean (Appointment)
            Appointment::create([
                'patient_id' => $patient->id,
                'clinic_id' => $request->clinic_id,
                'doctor_id' => $request->doctor_id,
                'status' => 'waiting', 
                'estimated_time' => now()->addMinutes(15), 
            ]);

            DB::commit();

            return back()->with('success', "Pasien {$patient->name} berhasil didaftarkan dengan No RM: {$nomorRM}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mendaftar: ' . $e->getMessage()]);
        }
    }
}