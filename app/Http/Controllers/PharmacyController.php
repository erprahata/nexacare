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
        // 1. Tarik Antrean Aktif ('pending')
        $prescriptions = Prescription::with([
            'medicalRecord.appointment.patient', 
            'items.medicine'
        ])
        ->where('status', 'pending')
        ->latest()
        ->get();

        // 2. Tarik Riwayat Hari Ini ('completed')
        $history = Prescription::with([
            'medicalRecord.appointment.patient', 
            'items.medicine'
        ])
        ->where('status', 'completed')
        ->whereDate('updated_at', today()) // Hanya riwayat hari ini agar query tidak berat
        ->latest()
        ->get();

        return Inertia::render('Farmasi', [ // Pastikan nama komponen sesuai dengan struktur Anda
            'prescriptions' => $prescriptions,
            'history' => $history
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