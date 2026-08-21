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

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
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

const printReceipt = () => {
    window.print();
};

const finishTransaction = () => {
    paymentSuccess.value = false;
    selectedInvoice.value = null;
    amountTendered.value = '';
};
</script>

<template>
    <Head title="Dashboard Kasir" />

    <!-- Elemen utama UI yang akan disembunyikan paksa saat mode Print -->
    <div class="ui-container">
        <AuthenticatedLayout class="h-screen overflow-hidden">
            <template #header>
                <h2 class="font-semibold text-2xl text-slate-800 leading-tight">Dashboard Kasir <span class="text-emerald-600 font-light">| Billing & Payment</span></h2>
            </template>

            <div class="p-4 sm:p-6 h-[calc(100vh-140px)] overflow-hidden">
                <div class="w-full h-full">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-full">
                        
                        <!-- PANEL KIRI -->
                        <div class="lg:col-span-1 bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-6 relative flex flex-col h-full overflow-hidden">
                            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2 border-b border-slate-200 pb-3 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                Menunggu Pembayaran
                            </h3>

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

                        <!-- PANEL KANAN -->
                        <div class="lg:col-span-3 bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-6 lg:p-8 relative flex flex-col h-full overflow-hidden">
                            
                            <div v-if="selectedInvoice" class="h-full flex flex-col lg:flex-row gap-8 relative overflow-hidden">
                                
                                <div class="flex-1 flex flex-col h-full border-r border-slate-200/50 pr-8 overflow-hidden">
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
                                    
                                    <div class="pt-4 border-t border-dashed border-slate-300 flex justify-between items-end shrink-0">
                                        <span class="text-xl font-bold text-slate-700">Total Tagihan</span>
                                        <span class="text-4xl font-black text-emerald-600">{{ formatRupiah(selectedInvoice.total_amount) }}</span>
                                    </div>
                                </div>

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

                                        <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
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

                                    <button @click="processPayment" :disabled="isProcessing"
                                            class="mt-6 w-full shrink-0 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-4 px-8 rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50">
                                        {{ isProcessing ? 'Memproses...' : 'Proses Pembayaran' }}
                                    </button>
                                </div>

                                <!-- OVERLAY PREMIUM E-RECEIPT -->
                                <transition enter-active-class="transition ease-out duration-500" enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-300" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                                    <div v-if="paymentSuccess" class="absolute inset-0 z-50 bg-slate-900/60 backdrop-blur-md rounded-3xl flex flex-col items-center justify-center p-4 sm:p-8 shadow-2xl overflow-y-auto">
                                        
                                        <!-- DESAIN TIKET PREMIUM -->
                                        <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl mb-6 relative overflow-hidden flex flex-col">
                                            
                                            <!-- Ticket Header -->
                                            <div class="bg-gradient-to-br from-slate-800 to-slate-900 px-6 py-6 text-white text-center relative">
                                                <!-- Watermark Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 absolute -top-4 -right-4 text-white opacity-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                <h3 class="text-2xl font-black tracking-wider uppercase mb-1">NexaCare</h3>
                                                <p class="text-xs text-slate-300 font-medium tracking-widest">DIGITAL RECEIPT</p>
                                            </div>

                                            <!-- Ticket Body -->
                                            <div class="p-6 bg-white">
                                                <div class="flex justify-between items-center mb-6">
                                                    <div>
                                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Waktu Transaksi</p>
                                                        <p class="text-sm font-bold text-slate-700">{{ formatDate(new Date()) }}</p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Metode</p>
                                                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold text-xs rounded uppercase">{{ paymentMethod }}</span>
                                                    </div>
                                                </div>

                                                <div class="mb-5">
                                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Nama Pasien</p>
                                                    <p class="text-base font-black text-slate-800">{{ selectedInvoice.appointment.patient.name }}</p>
                                                    <p class="text-xs text-slate-500 font-mono mt-0.5">INV: {{ selectedInvoice.id.substring(0, 12).toUpperCase() }}</p>
                                                </div>

                                                <!-- Items List -->
                                                <div class="space-y-3 mb-6">
                                                    <div v-for="item in selectedInvoice.items" :key="'rec-'+item.id" class="flex justify-between items-start text-sm">
                                                        <span class="text-slate-600 pr-4 leading-tight">{{ item.description }}</span>
                                                        <span class="font-bold text-slate-800 whitespace-nowrap">{{ formatRupiah(item.amount) }}</span>
                                                    </div>
                                                </div>

                                                <!-- Summary Block -->
                                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                                    <div class="flex justify-between items-center mb-2">
                                                        <span class="text-xs font-bold text-slate-500 uppercase">Total Dibayar</span>
                                                        <span class="text-lg font-black text-emerald-600">{{ formatRupiah(selectedInvoice.total_amount) }}</span>
                                                    </div>
                                                    <div v-if="paymentMethod === 'cash'" class="flex justify-between text-xs text-slate-500 mb-1">
                                                        <span>Uang Tunai</span> <span>{{ formatRupiah(amountTendered) }}</span>
                                                    </div>
                                                    <div v-if="paymentMethod === 'cash'" class="flex justify-between text-xs text-slate-500">
                                                        <span>Kembalian</span> <span>{{ formatRupiah(changeAmount) }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Perforated Line Effect (Garis Putus-putus Tiket) -->
                                            <div class="relative flex items-center justify-between px-2 py-1 bg-white">
                                                <div class="w-full border-t-2 border-dashed border-slate-200"></div>
                                                <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-slate-900/60 rounded-full shadow-inner"></div>
                                                <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 bg-slate-900/60 rounded-full shadow-inner"></div>
                                            </div>

                                            <!-- Ticket Footer (Barcode & Status) -->
                                            <div class="bg-white p-6 text-center flex flex-col items-center">
                                                <!-- SVG Barcode (Ilustrasi) -->
                                                <svg class="h-10 w-48 text-slate-800 mb-4 opacity-80" viewBox="0 0 200 40" preserveAspectRatio="none">
                                                    <path fill="currentColor" d="M0 0h4v40H0zM6 0h2v40H6zM12 0h6v40h-6zM22 0h2v40h-2zM28 0h4v40h-4zM36 0h2v40h-2zM42 0h6v40h-6zM52 0h4v40h-4zM58 0h2v40h-2zM64 0h6v40h-6zM74 0h4v40h-4zM80 0h2v40h-2zM86 0h8v40h-8zM98 0h2v40h-2zM104 0h4v40h-4zM112 0h6v40h-6zM122 0h2v40h-2zM128 0h4v40h-4zM136 0h6v40h-6zM146 0h4v40h-4zM152 0h2v40h-2zM158 0h8v40h-8zM170 0h2v40h-2zM176 0h6v40h-6zM186 0h4v40h-4zM194 0h6v40h-6z"/>
                                                </svg>
                                                <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 font-black tracking-widest rounded-full text-xs border border-emerald-200">
                                                    PAID IN FULL
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Tombol Aksi (Tidak ikut dicetak) -->
                                        <div class="flex gap-4 w-full max-w-sm">
                                            <button @click="printReceipt" class="flex-1 bg-white hover:bg-slate-50 text-slate-800 font-bold py-3.5 px-4 rounded-xl transition-all shadow-lg flex justify-center items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                                Cetak Struk
                                            </button>
                                            <button @click="finishTransaction" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 px-4 rounded-xl transition-all shadow-lg">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                            
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
    </div>

    <!-- AREA CETAK KHUSUS (Thermal Print 80mm) -->
    <div id="print-area" v-if="paymentSuccess && selectedInvoice">
        <div class="text-center mb-4 border-b border-dashed border-black pb-2">
            <h3 class="text-base font-bold uppercase">NexaCare</h3>
            <p class="text-xs">Tanda Terima Pembayaran</p>
        </div>
        <div class="mb-4 text-xs font-mono">
            <div>Waktu : {{ formatDate(new Date()) }}</div>
            <div>No.Inv: {{ selectedInvoice.id.substring(0, 8) }}</div>
            <div>Pasien: {{ selectedInvoice.appointment.patient.name }}</div>
            <div>Metode: {{ paymentMethod.toUpperCase() }}</div>
        </div>
        <div class="border-t border-b border-dashed border-black py-2 mb-4 space-y-1 text-xs font-mono">
            <div v-for="item in selectedInvoice.items" :key="'p-'+item.id" class="flex justify-between">
                <span class="w-2/3 pr-2 whitespace-normal break-words">{{ item.description }}</span>
                <span class="w-1/3 text-right">{{ formatRupiah(item.amount) }}</span>
            </div>
        </div>
        <div class="space-y-1 mb-4 text-xs font-bold font-mono">
            <div class="flex justify-between"><span>TOTAL</span> <span>{{ formatRupiah(selectedInvoice.total_amount) }}</span></div>
            <div v-if="paymentMethod === 'cash'" class="flex justify-between font-normal"><span>Tunai</span> <span>{{ formatRupiah(amountTendered) }}</span></div>
            <div v-if="paymentMethod === 'cash'" class="flex justify-between font-normal"><span>Kembali</span> <span>{{ formatRupiah(changeAmount) }}</span></div>
        </div>
        <div class="text-center text-xs font-mono">
            <p>*** LUNAS ***</p>
            <p class="mt-3 text-[10px]">Terima kasih atas kunjungan Anda.<br>Semoga lekas sembuh.</p>
        </div>
    </div>
</template>

<style>
/* CSS SUPER AGRESIF KHUSUS UNTUK MODE CETAK */
@media print {
    /* Sembunyikan seluruh UI Dashboard */
    .ui-container {
        display: none !important;
    }
    
    /* Sembunyikan body background color */
    body {
        background-color: white !important;
        background-image: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Pastikan hanya print-area yang dirender, cocok untuk thermal 80mm */
    #print-area {
        display: block !important;
        position: absolute;
        left: 0;
        top: 0;
        width: 80mm;
        margin: 0;
        padding: 0 5px;
        color: black;
        font-family: monospace;
    }

    /* Hilangkan header/footer default browser pada kertas */
    @page {
        margin: 0;
    }
}

/* Sembunyikan print-area di layar monitor normal */
@media screen {
    #print-area {
        display: none !important;
    }
}
</style>