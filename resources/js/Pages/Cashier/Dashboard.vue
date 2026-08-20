<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    invoices: {
        type: Array,
        default: () => []
    }
});

const selectedInvoice = ref(null);
const paymentMethod = ref('cash');
const isProcessing = ref(false);
const paymentSuccess = ref(false); 

const amountTendered = ref(''); 

const changeAmount = computed(() => {
    if (!selectedInvoice.value || !amountTendered.value) return 0;
    const diff = parseInt(amountTendered.value) - selectedInvoice.value.total_amount;
    return diff > 0 ? diff : 0;
});

const formatRupiah = (angka) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
};

const selectInvoice = (invoice) => {
    selectedInvoice.value = invoice;
    paymentMethod.value = 'cash';
    amountTendered.value = ''; 
    paymentSuccess.value = false;
};

const processPayment = () => {
    if (!selectedInvoice.value) return;

    if (paymentMethod.value === 'cash') {
        if (!amountTendered.value || parseInt(amountTendered.value) < selectedInvoice.value.total_amount) {
            alert('Nominal uang yang diterima kurang dari total tagihan!');
            return;
        }
    }
    
    isProcessing.value = true;
    
    router.put(route('kasir.bayar', selectedInvoice.value.id), {
        payment_method: paymentMethod.value,
        amount_tendered: paymentMethod.value === 'cash' ? amountTendered.value : null
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isProcessing.value = false;
            paymentSuccess.value = true; 
        },
        onError: () => {
            isProcessing.value = false;
        }
    });
};

const finishTransaction = () => {
    paymentSuccess.value = false;
    selectedInvoice.value = null;
    amountTendered.value = '';
};
</script>

<template>
    <Head title="Dashboard Kasir" />

    <!-- Supaya scroll global benar-benar mati, kita pastikan layout tidak melebihi tinggi layar (100vh) -->
    <AuthenticatedLayout class="h-screen overflow-hidden">
        <template #header>
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">Dashboard Kasir <span class="text-emerald-600 font-light">| Billing & Payment</span></h2>
        </template>

        <!-- KUNCI FULLSCREEN: h-[calc(100vh-140px)] dan hapus padding vertikal yang berlebih -->
        <div class="p-4 sm:p-6 h-[calc(100vh-140px)] overflow-hidden">
            <div class="w-full h-full">
                <!-- KUNCI GRID: Pastikan tingginya h-full mengikuti parent -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-full">
                    
                    <!-- PANEL KIRI: h-full (Hapus min-h) -->
                    <div class="lg:col-span-1 bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-6 relative flex flex-col h-full overflow-hidden">
                        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                            Menunggu Pembayaran
                        </h3>

                        <!-- AREA SCROLL KIRI: Hanya daftar tagihan yang bisa di-scroll -->
                        <div class="overflow-y-auto pr-2 space-y-3 flex-1">
                            <div v-for="invoice in invoices" :key="invoice.id" 
                                 @click="selectInvoice(invoice)"
                                 class="p-4 rounded-2xl border cursor-pointer transition-all duration-200"
                                 :class="selectedInvoice?.id === invoice.id ? 'bg-emerald-50 border-emerald-300 shadow-md transform scale-[1.02]' : 'bg-white/60 border-white/80 hover:bg-white/80'">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-slate-700">{{ invoice.appointment.patient.name }}</span>
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">UNPAID</span>
                                </div>
                                <p class="text-xs text-slate-500 mb-2">RM: {{ invoice.appointment.patient.medical_record_number }}</p>
                                <p class="text-sm font-bold text-emerald-600">{{ formatRupiah(invoice.total_amount) }}</p>
                            </div>
                            
                            <div v-if="invoices.length === 0" class="text-center text-slate-400 py-10 text-sm">
                                Tidak ada antrean tagihan.
                            </div>
                        </div>
                    </div>

                    <!-- PANEL KANAN: h-full (Hapus min-h) -->
                    <div class="lg:col-span-3 bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-6 lg:p-8 relative flex flex-col h-full overflow-hidden">
                        
                        <div v-if="selectedInvoice" class="h-full flex flex-col lg:flex-row gap-8 relative overflow-hidden">
                            
                            <!-- SUB-PANEL KIRI: Rincian Tagihan -->
                            <div class="flex-1 flex flex-col h-full border-r border-slate-200/50 pr-8 overflow-hidden">
                                <!-- Header Detail Pasien (shrink-0 agar tidak ikut mengecil) -->
                                <div class="flex justify-between items-end border-b border-slate-200 pb-4 mb-4 shrink-0">
                                    <div>
                                        <h2 class="text-3xl font-extrabold text-slate-800">{{ selectedInvoice.appointment.patient.name }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">Poli: {{ selectedInvoice.appointment.clinic.name }} | dr. {{ selectedInvoice.appointment.doctor.name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-400 mb-1">Invoice ID</p>
                                        <p class="text-xs font-mono bg-slate-100/80 px-2 py-1 rounded text-slate-600 border border-slate-200">{{ selectedInvoice.id }}</p>
                                    </div>
                                </div>

                                <!-- AREA SCROLL TENGAH: Hanya Rincian Tindakan yang bisa di-scroll -->
                                <div class="flex-1 overflow-y-auto mb-4 bg-slate-50/40 rounded-2xl p-6 border border-slate-100/50">
                                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-5">Rincian Tindakan & Obat</h4>
                                    <div class="space-y-4">
                                        <div v-for="item in selectedInvoice.items" :key="item.id" class="flex justify-between items-center text-base">
                                            <div class="flex items-center gap-3">
                                                <div class="w-2.5 h-2.5 rounded-full" :class="item.item_type === 'medicine' ? 'bg-teal-400' : 'bg-indigo-400'"></div>
                                                <span class="text-slate-700 font-medium">{{ item.description }}</span>
                                            </div>
                                            <span class="font-bold text-slate-800">{{ formatRupiah(item.amount) }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Total Tagihan (shrink-0 agar tetap menempel di bawah) -->
                                <div class="pt-4 border-t border-dashed border-slate-300 flex justify-between items-end shrink-0">
                                    <span class="text-xl font-bold text-slate-700">Total Tagihan</span>
                                    <span class="text-4xl font-black text-emerald-600">{{ formatRupiah(selectedInvoice.total_amount) }}</span>
                                </div>
                            </div>

                            <!-- SUB-PANEL KANAN: Kalkulator & Metode Pembayaran -->
                            <div class="w-full lg:w-[380px] flex flex-col h-full shrink-0 overflow-y-auto pr-2">
                                <div class="mb-auto">
                                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Metode Pembayaran</h4>
                                    <div class="grid grid-cols-2 gap-3 mb-6">
                                        <label class="cursor-pointer">
                                            <input type="radio" v-model="paymentMethod" value="cash" class="peer sr-only">
                                            <div class="p-3 text-center border-2 border-transparent bg-white/60 rounded-xl shadow-sm peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-white/80 transition-all">
                                                <span class="block text-sm font-bold text-slate-700">Tunai</span>
                                            </div>
                                        </label>
                                        <label class="cursor-pointer relative">
                                            <input type="radio" v-model="paymentMethod" value="qris" class="peer sr-only">
                                            <div class="p-3 text-center border-2 border-transparent bg-white/60 rounded-xl shadow-sm peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-white/80 transition-all">
                                                <span class="absolute -top-2 -right-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-sm">PG</span>
                                                <span class="block text-sm font-bold text-slate-700">QRIS / VA</span>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Kalkulator Cash -->
                                    <transition
                                        enter-active-class="transition ease-out duration-200"
                                        enter-from-class="opacity-0 -translate-y-2"
                                        enter-to-class="opacity-100 translate-y-0"
                                    >
                                        <div v-if="paymentMethod === 'cash'" class="bg-white/40 border border-white/60 p-5 rounded-2xl shadow-inner space-y-5">
                                            <div>
                                                <label class="block text-sm font-bold text-slate-500 mb-2">Uang Diterima (Rp)</label>
                                                <input type="number" v-model="amountTendered" placeholder="0" 
                                                       class="w-full bg-white/70 border-slate-200 rounded-xl text-slate-800 text-xl font-bold py-3 focus:ring-emerald-500 focus:border-emerald-500">
                                            </div>
                                            <div class="flex flex-col bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                                                <span class="text-sm font-bold text-slate-500 mb-1">Kembalian:</span>
                                                <span class="text-3xl font-black text-slate-800">{{ formatRupiah(changeAmount) }}</span>
                                            </div>
                                        </div>
                                    </transition>
                                </div>

                                <!-- Tombol Eksekusi -->
                                <button @click="processPayment" :disabled="isProcessing"
                                        class="mt-6 w-full shrink-0 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50">
                                    {{ isProcessing ? 'Memproses...' : (paymentMethod === 'cash' ? 'Terima & Cetak Struk' : 'Buat Tagihan Gateway') }}
                                </button>
                            </div>

                            <!-- OVERLAY ANIMASI SUKSES -->
                            <transition
                                enter-active-class="transition ease-out duration-500"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-300"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div v-if="paymentSuccess" class="absolute inset-0 z-50 bg-white/90 backdrop-blur-md rounded-3xl flex flex-col items-center justify-center p-8 text-center shadow-2xl border border-white/50">
                                    <div class="w-28 h-28 bg-emerald-100 rounded-full flex items-center justify-center mb-6 shadow-inner animate-bounce">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <h2 class="text-4xl font-extrabold text-slate-800 mb-3">Pembayaran Lunas!</h2>
                                    <p class="text-lg text-slate-500 mb-8">Transaksi pasien <strong>{{ selectedInvoice.appointment.patient.name }}</strong> dicatat.</p>
                                    
                                    <div v-if="paymentMethod === 'cash'" class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 mb-8 w-full max-w-md">
                                        <p class="text-base font-bold text-slate-600 mb-2">Berikan Kembalian:</p>
                                        <p class="text-5xl font-black text-emerald-600">{{ formatRupiah(changeAmount) }}</p>
                                    </div>

                                    <button @click="finishTransaction" 
                                            class="w-full max-w-sm bg-slate-800 hover:bg-slate-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-md hover:shadow-lg text-lg">
                                        Tutup & Lanjut Antrean
                                    </button>
                                </div>
                            </transition>
                        </div>
                        
                        <!-- State Kosong -->
                        <div v-else class="h-full flex flex-col items-center justify-center text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-xl font-medium">Pilih tagihan dari daftar di sebelah kiri</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>