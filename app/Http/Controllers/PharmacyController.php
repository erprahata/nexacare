<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use Inertia\Inertia;

class PharmacyController extends Controller
{
    // 1. Menampilkan Halaman Dashboard Farmasi
    public function index()
    {
        // Tarik data resep yang berstatus 'pending', beserta relasi pasien dan detail obatnya
        $prescriptions = Prescription::with([
            'medicalRecord.appointment.patient', 
            'items.medicine'
        ])
        ->where('status', 'pending')
        ->latest()
        ->get();

        // Asumsi nama file Vue Anda ada di folder Pharmacy/Dashboard.vue 
        // (Sesuaikan jika nama/lokasi file Vue Anda berbeda)
        return Inertia::render('Pharmacy/Dashboard', [
            'prescriptions' => $prescriptions
        ]);
    }

    // 2. Memproses Tombol "Selesai Diracik"
    public function markAsCompleted($id)
    {
        try {
            $prescription = Prescription::findOrFail($id);

            // Ubah status resep menjadi completed
            $prescription->update([
                'status' => 'completed'
            ]);

            return back()->with('success', 'Resep berhasil diracik dan siap diserahkan ke pasien!');
            
        } catch (\Exception $e) {
            // Catat ke log sistem
            \Illuminate\Support\Facades\Log::error('ERROR FARMASI: ' . $e->getMessage());
            
            // Kirim pesan gagal ke GlassToast
            return back()->withErrors(['error' => 'Gagal memperbarui status: ' . $e->getMessage()]);
        }
    }
}