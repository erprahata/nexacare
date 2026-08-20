<?php

use App\Models\Appointment;
use App\Models\Prescription;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\EmrController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// 1. Grup khusus Admin, Dokter, Perawat (Poli)
Route::middleware(['auth', 'verified', 'role:admin,doctor,nurse'])->group(function () {
    Route::get('/dashboard', function () {
        $appointments = \App\Models\Appointment::with(['patient', 'doctor', 'clinic'])
            ->orderBy('estimated_time', 'asc')
            ->get();

        return Inertia::render('Dashboard', [
            'appointments' => $appointments
        ]);
    })->name('dashboard');
});

// 2. Grup khusus Farmasi/Apoteker (dan Admin)
Route::middleware(['auth', 'verified', 'role:admin,pharmacist'])->group(function () {
    
    // Route untuk menampilkan halaman (Read)
    Route::get('/farmasi', function () {
        $prescriptions = App\Models\Prescription::with(['medicalRecord.appointment.patient', 'items.medicine'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Farmasi', [
            'prescriptions' => $prescriptions
        ]);
    })->name('farmasi');

    // Route untuk mengeksekusi pembaruan status (Update)
    Route::put('/farmasi/{id}/proses', function ($id) {
        $prescription = App\Models\Prescription::findOrFail($id);
        $prescription->update(['status' => 'completed']);

        // Redirect kembali ke halaman yang sama, Inertia akan me-render ulang datanya secara otomatis
        return back()->with('success', 'Resep obat berhasil diproses!');
    })->name('farmasi.proses');

});

// 3. Grup khusus Dokter (Pemeriksaan & EMR)
Route::middleware(['auth', 'verified', 'role:doctor'])->group(function () {
    // Menampilkan halaman form EMR
    Route::get('/emr/{appointment_id}', [EmrController::class, 'create'])->name('emr.create');
    
    // Memproses data submit EMR
    Route::post('/emr/{appointment_id}', [EmrController::class, 'store'])->name('emr.store');
});

// 4. Grup khusus Kasir / Keuangan
Route::middleware(['auth', 'verified', 'role:cashier'])->group(function () {
    // Halaman utama kasir
    Route::get('/kasir', [CashierController::class, 'index'])->name('kasir.dashboard');
    
    // Action memproses pembayaran (sesuai nama route di Vue)
    Route::put('/kasir/bayar/{id}', [CashierController::class, 'processPayment'])->name('kasir.bayar');
});

// 5. Modul Frontdesk / Pendaftaran
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/pendaftaran', [AdmissionController::class, 'create'])->name('pendaftaran.create');
    Route::post('/pendaftaran', [AdmissionController::class, 'store'])->name('pendaftaran.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
