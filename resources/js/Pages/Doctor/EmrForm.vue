<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GlassButton from '@/Components/GlassButton.vue';
import GlassSelect from '@/Components/GlassSelect.vue'; 
import GlassInput from '@/Components/GlassInput.vue';
import GlassTextarea from '@/Components/GlassTextarea.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    appointment: Object,
    medicines: Array,
});

// Memformat data obat
const medicineOptions = computed(() => {
    return props.medicines.map(med => ({
        id: med.id,
        label: `${med.name} (Sisa Stok: ${med.stock})`
    }));
});

// Menghitung Format DOB dan Usia
const patientDetails = computed(() => {
    const dobString = props.appointment.patient.dob;
    if (!dobString) return { date: '-', age: '-' };

    const dob = new Date(dobString);
    const today = new Date();

    // Format DD-MM-YYYY
    const d = String(dob.getDate()).padStart(2, '0');
    const m = String(dob.getMonth() + 1).padStart(2, '0');
    const y = dob.getFullYear();
    const formattedDate = `${d}-${m}-${y}`;

    // Kalkulasi Umur (Tahun dan Bulan)
    let years = today.getFullYear() - y;
    let months = today.getMonth() - dob.getMonth();

    if (today.getDate() < dob.getDate()) {
        months--;
    }
    
    if (months < 0) {
        years--;
        months += 12;
    }

    return {
        date: formattedDate,
        age: `${years} Tahun ${months} Bulan`
    };
});

const form = useForm({
    diagnosis: '',
    doctor_notes: '',
    prescriptions: [
        { medicine_id: null, quantity: 1, dosage_instructions: '' }
    ]
});

const addPrescription = () => {
    form.prescriptions.push({ medicine_id: null, quantity: 1, dosage_instructions: '' });
};

const removePrescription = (index) => {
    form.prescriptions.splice(index, 1);
};

const submitEmr = () => {
    form.post(route('emr.store', props.appointment.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Input Rekam Medis" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">
                Rekam Medis <span class="text-indigo-600 font-light">| EMR Dokter</span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <form @submit.prevent="submitEmr" class="space-y-6">
                    
                    <div v-if="Object.keys(form.errors).length > 0 || $page.props.errors.error" 
                         class="bg-red-50/80 backdrop-blur-md border border-red-200 text-red-700 px-6 py-4 rounded-2xl shadow-sm">
                        <h5 class="font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            Gagal Menyimpan Data!
                        </h5>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                            <li v-if="$page.props.errors.error">{{ $page.props.errors.error }}</li>
                        </ul>
                    </div>

                    <!-- REVISI: Info Pasien dengan DOB dan Usia -->
                    <div class="bg-gradient-to-r from-indigo-500 to-blue-600 rounded-3xl p-6 shadow-lg text-white relative overflow-hidden">
                        <div class="absolute right-0 top-0 w-64 h-64 bg-white opacity-10 rounded-full -translate-y-1/2 translate-x-1/3"></div>
                        
                        <div class="flex flex-col md:flex-row md:items-end justify-between relative z-10 gap-4">
                            <div>
                                <h3 class="text-sm font-medium text-indigo-100 mb-1 tracking-wide uppercase">Pemeriksaan Pasien</h3>
                                <div class="text-3xl font-bold mb-2">{{ appointment.patient.name }}</div>
                                
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-sm font-mono border border-white/30 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 opacity-75" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                        {{ appointment.patient.medical_record_number }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-4 rounded-2xl flex items-center gap-6">
                                <div>
                                    <div class="text-xs text-indigo-200 font-medium mb-0.5">Tanggal Lahir</div>
                                    <div class="font-bold text-white tracking-wide">{{ patientDetails.date }}</div>
                                </div>
                                <div class="w-px h-8 bg-white/20"></div>
                                <div>
                                    <div class="text-xs text-indigo-200 font-medium mb-0.5">Usia</div>
                                    <div class="font-bold text-white">{{ patientDetails.age }}</div>
                                </div>
                            </div>
                            <!-- ALERT ALERGI -->
                        <div class="mt-5 p-3 rounded-xl border backdrop-blur-md flex items-start gap-3 transition-all"
                             :class="appointment.patient.allergies ? 'bg-red-500/30 border-red-300/60 shadow-[0_0_15px_rgba(239,68,68,0.3)]' : 'bg-emerald-500/20 border-emerald-300/40'">
                            <div class="mt-0.5">
                                <!-- Ikon Warning (Merah) -->
                                <svg v-if="appointment.patient.allergies" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-200 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <!-- Ikon Centang (Hijau) -->
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider mb-0.5" :class="appointment.patient.allergies ? 'text-red-200' : 'text-emerald-200'">
                                    Status Alergi Obat & Makanan
                                </div>
                                <div class="text-sm font-semibold" :class="appointment.patient.allergies ? 'text-white' : 'text-emerald-50'">
                                    {{ appointment.patient.allergies || 'Tidak ada riwayat alergi yang diketahui.' }}
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- KOLOM KIRI: Diagnosis -->
                        <div class="bg-white/50 backdrop-blur-xl border border-white/60 shadow-xl rounded-3xl p-8 relative">
                            <h4 class="text-xl font-bold text-slate-800 mb-6 border-b border-slate-200/60 pb-3 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Hasil Pemeriksaan
                            </h4>

                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Diagnosis Utama</label>
                                    <GlassTextarea v-model="form.diagnosis" rows="3" required placeholder="Tuliskan diagnosis penyakit..." />
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Catatan Dokter (Instruksi)</label>
                                    <GlassTextarea v-model="form.doctor_notes" rows="4" placeholder="Anjuran istirahat, pantangan makanan, dll..." />
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: E-Prescription -->
                        <div class="bg-white/50 backdrop-blur-xl border border-white/60 shadow-xl rounded-3xl p-8 relative overflow-visible">
                            <h4 class="text-xl font-bold text-slate-800 mb-6 border-b border-slate-200/60 pb-3 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    Resep Obat (E-Prescription)
                                </div>
                                <button type="button" @click="addPrescription" class="text-xs bg-teal-100 hover:bg-teal-200 text-teal-800 font-bold py-1.5 px-3 rounded-lg transition">
                                    + Tambah Obat
                                </button>
                            </h4>

                            <div class="space-y-4">
                                <div v-for="(item, index) in form.prescriptions" :key="index" 
                                     class="p-4 bg-slate-50/60 border border-slate-200 rounded-2xl relative group">
                                    
                                    <button v-if="form.prescriptions.length > 1" @click="removePrescription(index)" type="button" 
                                            class="absolute -top-2 -right-2 bg-red-100 text-red-600 rounded-full p-1 shadow-sm hover:bg-red-500 hover:text-white transition z-10">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div class="grid grid-cols-3 gap-4 mb-4">
                                        <div class="col-span-2">
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Pilih Obat</label>
                                            
                                            <!-- PENGGUNAAN DROPDOWN YG SUDAH KITA REVISI JADI BISA SEARCH -->
                                            <GlassSelect 
                                                v-model="item.medicine_id" 
                                                :options="medicineOptions" 
                                                placeholder="Cari Obat..."
                                            />

                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 mb-1">Jumlah</label>
                                            <GlassInput type="number" v-model="item.quantity" min="1" required class="text-sm px-4 py-2.5" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1">Aturan Pakai (Dosis)</label>
                                             <GlassInput type="text" v-model="item.dosage_instructions" required placeholder="Contoh: 3x sehari 1 tablet sesudah makan" class="text-sm px-4 py-2.5" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <GlassButton type="submit" :disabled="form.processing"
                            class="w-full sm:w-auto py-3 px-8 rounded-xl">
                            {{ form.processing ? 'Menyimpan & Memproses...' : 'Simpan Rekam Medis & Terbitkan Tagihan' }}
                        </GlassButton>
                    </div>

                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>