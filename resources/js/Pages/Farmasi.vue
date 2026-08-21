<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

// MENAMBAHKAN DEFAULT VALUE AGAR TIDAK CRASH JIKA DATA KOSONG
const props = defineProps({
    prescriptions: {
        type: Array,
        default: () => []
    },
    history: {
        type: Array,
        default: () => []
    },
});

const activeTab = ref('antrean');

const prosesResep = (id) => {
    router.put(route('farmasi.proses', id), {}, {
        preserveScroll: true,
    });
};

let pollingInterval;

onMounted(() => {
    pollingInterval = setInterval(() => {
        router.reload({ 
            only: ['prescriptions', 'history'],
            preserveState: true, 
            preserveScroll: true 
        });
    }, 10000); 
});

onUnmounted(() => {
    clearInterval(pollingInterval);
});

const formatTime = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Dashboard Apotek" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">Dashboard Farmasi <span class="text-teal-600 font-light">| E-Prescription</span></h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-8 relative overflow-hidden min-h-[600px]">
                    
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-teal-100 to-transparent opacity-50 rounded-bl-full pointer-events-none"></div>

                    <!-- HEADER & NAVIGASI TAB -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 relative z-10 gap-4">
                        
                        <div class="flex bg-white/40 border border-white/60 p-1.5 rounded-2xl shadow-sm backdrop-blur-md">
                            <button @click="activeTab = 'antrean'" 
                                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 flex items-center gap-2"
                                    :class="activeTab === 'antrean' ? 'bg-teal-500 text-white shadow-md' : 'text-slate-500 hover:text-teal-600 hover:bg-white/50'">
                                Antrean Masuk
                                <span v-if="prescriptions && prescriptions.length > 0" class="px-2 py-0.5 bg-red-400 text-white text-[10px] rounded-full">{{ prescriptions.length }}</span>
                            </button>
                            <button @click="activeTab = 'riwayat'" 
                                    class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all duration-300"
                                    :class="activeTab === 'riwayat' ? 'bg-slate-700 text-white shadow-md' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50'">
                                Riwayat Hari Ini
                            </button>
                        </div>

                        <span class="flex items-center gap-2 text-xs font-bold text-teal-600 bg-teal-50 border border-teal-100 px-4 py-2 rounded-full shadow-sm">
                            <span class="relative flex h-2.5 w-2.5">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-teal-500"></span>
                            </span>
                            Live Sync Active
                        </span>
                    </div>
                    
                    <!-- KONTEN TAB: ANTREAN -->
                    <div v-show="activeTab === 'antrean'" class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10 animate-fade-in">
                        <div v-for="resep in prescriptions" :key="resep.id" 
                             class="group bg-white/70 backdrop-blur-md border border-white/80 hover:border-teal-300 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                            
                            <!-- TAMBAHKAN TANDA TANYA (?.) UNTUK MENCEGAH CRASH JIKA RELASI KOSONG -->
                            <div class="flex justify-between items-start border-b border-slate-200 pb-3 mb-4">
                                <div>
                                    <h4 class="font-bold text-lg text-slate-800 group-hover:text-teal-700 transition-colors">
                                        {{ resep.medical_record?.appointment?.patient?.name || 'Pasien Tidak Diketahui' }}
                                    </h4>
                                    <p class="text-xs text-slate-500 font-medium bg-slate-100 px-2 py-1 rounded-md inline-block mt-1">
                                        RM: {{ resep.medical_record?.appointment?.patient?.medical_record_number || '-' }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-rose-400 text-white text-xs font-bold rounded-full shadow-md animate-pulse whitespace-nowrap">
                                    PERLU DIRACIK
                                </span>
                            </div>

                            <div v-if="resep.medical_record?.appointment?.patient?.allergies" 
                                 class="mb-4 bg-red-50/80 border border-red-200 rounded-lg p-3 flex items-start gap-2 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <span class="block text-xs font-bold text-red-700 uppercase tracking-wide">Peringatan Alergi!</span>
                                    <span class="block text-sm font-medium text-red-600">{{ resep.medical_record.appointment.patient.allergies }}</span>
                                </div>
                            </div>
                            
                            <ul class="text-sm text-slate-700 space-y-2 mb-6 flex-grow">
                                <li v-for="item in resep.items" :key="item.id" class="flex flex-col bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-slate-800">{{ item.medicine?.name || 'Obat Tidak Diketahui' }}</span>
                                        <span class="font-mono text-teal-600 bg-teal-50 px-2 rounded">{{ item.quantity }} pcs</span>
                                    </div>
                                    <span class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                        {{ item.dosage_instructions }}
                                    </span>
                                </li>
                            </ul>

                            <div class="text-right mt-auto pt-4 border-t border-slate-200/50">
                                <button @click="prosesResep(resep.id)" 
                                        class="w-full bg-gradient-to-r from-teal-500 to-emerald-400 hover:from-teal-600 hover:to-emerald-500 text-white text-sm font-bold py-3 px-6 rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                    Selesai & Siap Serahkan
                                </button>
                            </div>
                        </div>

                        <div v-if="!prescriptions || prescriptions.length === 0" class="col-span-full py-20 px-4 flex flex-col items-center justify-center bg-slate-50/30 rounded-3xl border border-dashed border-slate-300">
                            <div class="w-24 h-24 bg-teal-100 rounded-full flex items-center justify-center mb-5 shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-slate-600 text-xl font-bold text-center">Antrean Bersih!</p>
                            <p class="text-slate-500 text-sm mt-2 text-center max-w-sm">Semua resep sudah diselesaikan. Menunggu resep baru dari ruang periksa dokter.</p>
                        </div>
                    </div>

                    <!-- KONTEN TAB: RIWAYAT -->
                    <div v-show="activeTab === 'riwayat'" class="relative z-10 animate-fade-in">
                        <div class="bg-white/40 border border-white/60 rounded-2xl overflow-hidden shadow-sm">
                            <table class="min-w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-100/50 border-b border-slate-200/60">
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Pasien</th>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider w-1/2">Detail Obat</th>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Waktu Selesai</th>
                                        <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200/50">
                                    <tr v-for="hist in history" :key="'h-'+hist.id" class="hover:bg-white/60 transition-colors duration-200">
                                        <td class="py-4 px-6">
                                            <!-- TAMBAHKAN TANDA TANYA (?.) DI SINI JUGA -->
                                            <div class="font-bold text-sm text-slate-800">{{ hist.medical_record?.appointment?.patient?.name || 'Tidak Diketahui' }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ hist.medical_record?.appointment?.patient?.medical_record_number || '-' }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex flex-wrap gap-2">
                                                <span v-for="item in hist.items" :key="'hi-'+item.id" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/60 border border-slate-200 rounded-lg text-xs text-slate-700 shadow-sm">
                                                    <span class="font-semibold">{{ item.medicine?.name || 'Obat' }}</span>
                                                    <span class="text-[10px] bg-slate-200 px-1.5 py-0.5 rounded text-slate-600">{{ item.quantity }}x</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-sm font-mono text-slate-500">
                                            {{ formatTime(hist.updated_at) }} WIB
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-bold rounded-full shadow-sm inline-block">
                                                Selesai
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="!history || history.length === 0">
                                        <td colspan="4" class="py-12 text-center text-slate-500">
                                            Belum ada riwayat peracikan obat untuk hari ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>