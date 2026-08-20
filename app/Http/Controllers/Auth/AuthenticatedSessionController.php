<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // 1. Ambil data user yang baru saja login
        $user = $request->user();

        // 2. Tentukan waktu saat ini (Gunakan zona waktu WIB)
        // Fungsi now() bawaan Laravel sudah menggunakan Carbon
        $jam = now()->timezone('Asia/Jakarta')->format('H');

        // 3. Logika Sapaan Berdasarkan Jam
        if ($jam >= 5 && $jam < 11) {
            $waktu = 'pagi';
        } elseif ($jam >= 11 && $jam < 15) {
            $waktu = 'siang';
        } elseif ($jam >= 15 && $jam < 18) {
            $waktu = 'sore';
        } else {
            $waktu = 'malam';
        }

        // 4. Susun pesan kustom
        $pesan = "Halo, selamat {$waktu} {$user->name}. Selamat bertugas!";

        // Cek Role dan Redirect ke Halaman yang Sesuai
        if ($user->role === 'pharmacist') {
            return redirect()->intended(route('farmasi', absolute: false))->with('success', $pesan);
        } elseif ($user->role === 'cashier') {
            // Tambahkan baris ini untuk role Kasir
            return redirect()->intended(route('kasir.dashboard', absolute: false))->with('success', $pesan);
        }

        // Default untuk Admin, Doctor, Nurse
        return redirect()->intended(route('dashboard', absolute: false))->with('success', $pesan);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
