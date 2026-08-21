<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Inertia\Inertia;

class CashierController extends Controller
{
    // 1. Menampilkan Dashboard Kasir
    public function index()
    {
        // Tarik tagihan yang belum dibayar, beserta detail pasien, dokter, poli, dan rincian itemnya
        $invoices = Invoice::with([
            'appointment.patient',
            'appointment.clinic',
            'appointment.doctor',
            'items'
        ])
        ->where('payment_status', 'unpaid')
        ->latest()
        ->get();

        return Inertia::render('Cashier/Dashboard', [
            'invoices' => $invoices
        ]);
    }

    // 2. Eksekusi Pembayaran
    public function processPayment(Request $request, $id)
    {
        // Validasi input metode pembayaran dari frontend
        $request->validate([
            'payment_method' => 'required|string|in:cash,qris,va'
        ]);

        $invoice = Invoice::findOrFail($id);

        // Update status pembayaran sekaligus merekam metodenya
        $invoice->update([
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method, // <-- INI YANG KURANG SEBELUMNYA
        ]);

        // Berikan notifikasi sukses
        return back()->with('success', 'Pembayaran berhasil diverifikasi dan diselesaikan!');
    }
}