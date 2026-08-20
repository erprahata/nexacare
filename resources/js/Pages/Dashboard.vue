<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// Tambahkan 'Link' di sebelah 'Head' pada baris di bawah ini
import { Head, Link } from '@inertiajs/vue3'; 

defineProps({
    appointments: Array,
});
</script>

<template>
    <Head title="Dashboard SIMRS" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">Dashboard <span class="text-blue-600 font-light">| Zero-Wait Queuing</span></h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Glassmorphism Container -->
                <div class="bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-8 relative overflow-hidden">
                    
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-blue-100 to-transparent opacity-60 rounded-bl-full pointer-events-none"></div>

                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2 relative z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Daftar Pasien Hari Ini
                    </h3>
                    
                    <!-- Table Modern -->
                    <div class="overflow-x-auto relative z-10 rounded-xl border border-slate-200/60 bg-white/40">
                        <table class="min-w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-100/50 border-b border-slate-200/60">
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">No. RM</th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Pasien</th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Poli & Dokter</th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">ETA (Jadwal)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200/50">
                                <tr v-for="apt in appointments" :key="apt.id" class="hover:bg-white/60 transition-colors duration-200">
                                    <td class="py-4 px-6 text-sm font-bold text-slate-700">
                                        {{ apt.patient.medical_record_number }}
                                    </td>
                                    <td class="py-4 px-6 text-sm text-slate-700">
                                        <div class="flex items-center gap-2">
                                            {{ apt.patient.name }}
                                            <span v-if="apt.patient.is_vvip" class="px-2 py-0.5 bg-gradient-to-r from-amber-200 to-yellow-400 text-yellow-900 text-[10px] font-bold rounded-full shadow-sm">VIP</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-slate-600">
                                        <div class="font-semibold">{{ apt.clinic.name }}</div>
                                        <div class="text-xs text-slate-400">{{ apt.doctor.name }}</div>
                                    </td>
                                    <!-- KODE BARU YANG DIREVISI -->
                                    <td class="py-4 px-6 text-sm">
                                        <!-- Cek apakah status masuk dalam array yang diizinkan untuk diperiksa -->
                                        <div v-if="['waiting', 'arrived', 'in_progress'].includes(apt.status)">
                                            <!-- Link menuju form EMR -->
                                            <Link :href="route('emr.create', apt.id)" 
                                                class="px-4 py-1.5 bg-indigo-100 hover:bg-indigo-600 text-indigo-700 hover:text-white text-xs font-bold rounded-full transition-colors duration-200 inline-block shadow-sm">
                                                Periksa Pasien
                                            </Link>
                                        </div>
                                        <div v-else>
                                            <!-- Dynamic Badge: Hijau untuk Completed, Abu-abu/Merah untuk lainnya -->
                                            <span class="px-3 py-1 border text-xs font-bold rounded-full capitalize inline-block shadow-sm"
                                                  :class="apt.status === 'completed' 
                                                    ? 'bg-emerald-50 text-emerald-600 border-emerald-200' 
                                                    : 'bg-slate-100 text-slate-500 border-slate-200'">
                                                {{ apt.status }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm font-mono text-slate-500 text-right">
                                        {{ new Date(apt.estimated_time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} WIB
                                    </td>
                                </tr>
                                <tr v-if="appointments.length === 0">
                                    <td colspan="5" class="py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada antrean pasien saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>