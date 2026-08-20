<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({
    prescriptions: Array,
});

const prosesResep = (id) => {
    // Kembalikan ke 'farmasi.proses' agar cocok dengan web.php Anda
    router.put(route('farmasi.proses', id), {}, {
        preserveScroll: true,
    });
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
                
                <!-- Card Container Utama (Glassmorphism) -->
                <div class="bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-8 relative overflow-hidden">
                    
                    <!-- Dekorasi tambahan dalam card -->
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-teal-100 to-transparent opacity-50 rounded-bl-full pointer-events-none"></div>

                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Antrean Resep Masuk
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                        <!-- Card Item Resep -->
                        <div v-for="resep in prescriptions" :key="resep.id" 
                             class="group bg-white/70 backdrop-blur-md border border-white/80 hover:border-teal-300 rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300">
                            
                            <div class="flex justify-between items-start border-b border-slate-200 pb-3 mb-4">
                                <div>
                                    <h4 class="font-bold text-lg text-slate-800 group-hover:text-teal-700 transition-colors">{{ resep.medical_record.appointment.patient.name }}</h4>
                                    <p class="text-xs text-slate-500 font-medium bg-slate-100 px-2 py-1 rounded-md inline-block mt-1">
                                        RM: {{ resep.medical_record.appointment.patient.medical_record_number }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 bg-gradient-to-r from-red-500 to-rose-400 text-white text-xs font-bold rounded-full shadow-md animate-pulse">
                                    PERLU DIRACIK
                                </span>
                            </div>
                            
                            <ul class="text-sm text-slate-700 space-y-2 mb-6">
                                <li v-for="item in resep.items" :key="item.id" class="flex flex-col bg-slate-50/50 p-2 rounded-lg border border-slate-100">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-slate-800">{{ item.medicine.name }}</span>
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

                            <div class="text-right mt-auto">
                                <button @click="prosesResep(resep.id)" 
                                        class="w-full sm:w-auto bg-gradient-to-r from-teal-500 to-emerald-400 hover:from-teal-600 hover:to-emerald-500 text-white text-sm font-bold py-2.5 px-6 rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                                    Selesai Diracik
                                </button>
                            </div>
                        </div>

                        <!-- Tampilan Jika Kosong (Glassmorphism style) -->
                        <div v-if="prescriptions.length === 0" class="col-span-full py-16 px-4 flex flex-col items-center justify-center bg-slate-50/30 rounded-2xl border border-dashed border-slate-300">
                            <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-slate-500 text-lg font-medium text-center">Bagus! Semua antrean resep sudah selesai diproses.</p>
                            <p class="text-slate-400 text-sm mt-1">Apotek saat ini kosong.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>