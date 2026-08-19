<?php

use App\Models\Appointment;
use App\Models\Prescription;
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
