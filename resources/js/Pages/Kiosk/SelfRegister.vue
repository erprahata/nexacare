<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    clinics: Array,
    doctors: Array,
});

const page = usePage();

const form = useForm({
    name: '',
    nik: '',
    dob: '',
    phone: '',
    address: '',
    allergies: '',
    clinic_id: '',
    doctor_id: '',
    is_vvip: false,
});

const step = ref(1);

const nextStep = () => { if (step.value < 3) step.value++; };
const prevStep = () => { if (step.value > 1) step.value--; };

const submit = () => {
    form.post(route('kiosk.store'), {
        preserveScroll: true,
        onSuccess: () => {
            step.value = 4; 
        },
    });
};

const resetKiosk = () => {
    form.reset();
    form.clearErrors();
    page.props.flash.success = null;
    step.value = 1;
};

// ==========================================
// LOGIKA CUSTOM DATE PICKER 
// ==========================================
const isDatePickerOpen = ref(false);
const currentDate = new Date();
const currentYear = ref(currentDate.getFullYear());
const currentMonth = ref(currentDate.getMonth());

const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

const years = computed(() => {
    const startYear = new Date().getFullYear();
    return Array.from({ length: 100 }, (_, i) => startYear - i);
});

const blankDays = computed(() => {
    const firstDay = new Date(currentYear.value, currentMonth.value, 1).getDay();
    return Array.from({ length: firstDay });
});

const monthDays = computed(() => {
    const daysInMonth = new Date(currentYear.value, currentMonth.value + 1, 0).getDate();
    return Array.from({ length: daysInMonth }, (_, i) => i + 1);
});

const selectDate = (day) => {
    const m = String(currentMonth.value + 1).padStart(2, '0');
    const d = String(day).padStart(2, '0');
    form.dob = `${currentYear.value}-${m}-${d}`;
    isDatePickerOpen.value = false;
    form.clearErrors('dob');
};

const formatDateDisplay = (dateStr) => {
    if (!dateStr) return '';
    const [y, m, d] = dateStr.split('-');
    return `${parseInt(d)} ${monthNames[parseInt(m) - 1]} ${y}`;
};
</script>

<template>
    <Head title="Self Service Registration" />

    <div class="min-h-screen flex flex-col justify-center items-center bg-[#f4f7fb] relative overflow-hidden font-sans select-none">
        
        <!-- ========================================== -->
        <!-- BACKGROUND & FLOATING OBJECTS (PREMIUM UI) -->
        <!-- ========================================== -->
        
        <!-- Mesh Gradient -->
        <div class="absolute top-[-20%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-gradient-to-br from-blue-300/40 to-indigo-300/20 blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-[-20%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-gradient-to-br from-teal-300/40 to-emerald-300/20 blur-[100px] pointer-events-none"></div>

        <!-- Floating Objects Kiri -->
        <div class="absolute left-[5%] top-[20%] animate-float pointer-events-none opacity-40">
            <div class="w-24 h-24 bg-white/40 backdrop-blur-md rounded-3xl border border-white/60 shadow-lg transform rotate-12 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            </div>
        </div>
        <div class="absolute left-[8%] bottom-[20%] animate-float-delayed pointer-events-none opacity-30">
            <div class="w-16 h-16 bg-gradient-to-tr from-blue-400/30 to-teal-400/30 backdrop-blur-lg rounded-full border border-white/50 shadow-xl"></div>
        </div>

        <!-- Floating Objects Kanan -->
        <div class="absolute right-[5%] top-[30%] animate-float-delayed-2 pointer-events-none opacity-40">
            <div class="w-20 h-20 bg-white/40 backdrop-blur-md rounded-[2rem] border border-white/60 shadow-lg transform -rotate-12 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
        </div>
        <div class="absolute right-[10%] bottom-[15%] animate-float pointer-events-none opacity-30">
            <div class="w-28 h-28 bg-white/20 backdrop-blur-md rounded-[2.5rem] border border-white/40 shadow-2xl transform rotate-45"></div>
        </div>

        <!-- ========================================== -->
        <!-- HEADER KIOSK -->
        <!-- ========================================== -->
        <div class="relative z-10 mb-8 flex flex-col items-center">
            <div class="w-20 h-20 bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl flex items-center justify-center shadow-2xl mb-4 border border-slate-700">
                <span class="text-white font-extrabold text-4xl tracking-tighter">N</span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">Nexa<span class="text-teal-600">Care</span></h1>
            <p class="text-base text-slate-500 font-bold uppercase tracking-widest mt-2 border-b-2 border-teal-500/30 pb-1">Kiosk Pendaftaran Mandiri</p>
        </div>

        <!-- ========================================== -->
        <!-- CONTAINER UTAMA -->
        <!-- ========================================== -->
        <div class="w-full max-w-3xl px-8 py-10 bg-white/40 bg-gradient-to-br from-white/60 to-white/20 backdrop-blur-3xl border border-white/70 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-[3rem] relative z-10">
            
            <!-- PROGRESS BAR -->
            <div v-if="step < 4" class="mb-10 relative">
                <div class="flex justify-between mb-3 px-2">
                    <span class="text-xs font-black uppercase tracking-wider" :class="step >= 1 ? 'text-teal-600' : 'text-slate-400'">1. Identitas</span>
                    <span class="text-xs font-black uppercase tracking-wider" :class="step >= 2 ? 'text-teal-600' : 'text-slate-400'">2. Kontak</span>
                    <span class="text-xs font-black uppercase tracking-wider" :class="step >= 3 ? 'text-teal-600' : 'text-slate-400'">3. Layanan</span>
                </div>
                <div class="h-3 w-full bg-slate-200/50 rounded-full overflow-hidden shadow-inner backdrop-blur-sm border border-white/60">
                    <div class="h-full bg-gradient-to-r from-teal-400 to-emerald-500 transition-all duration-700 ease-out rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)]"
                         :style="{ width: step === 1 ? '33%' : step === 2 ? '66%' : '100%' }"></div>
                </div>
            </div>

            <!-- Pesan Error Global -->
            <div v-if="Object.keys(form.errors).length > 0" class="mb-8 p-4 bg-rose-50/80 backdrop-blur-md border border-rose-200 text-rose-700 rounded-2xl shadow-sm">
                <p class="font-bold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Mohon periksa kembali isian Anda:
                </p>
                <ul class="list-disc list-inside text-sm mt-1 ml-1 font-medium">
                    <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                </ul>
            </div>

            <!-- FORM WIZARD -->
            <form @submit.prevent="submit" class="space-y-6">
                
                <!-- STEP 1: IDENTITAS UTAMA -->
                <transition enter-active-class="transition duration-500 ease-out" enter-from-class="opacity-0 translate-x-10" enter-to-class="opacity-100 translate-x-0" leave-active-class="hidden">
                    <div v-show="step === 1" class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 ml-2">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" inputmode="numeric" v-model="form.nik" 
                                   @input="form.nik = form.nik.replace(/[^0-9]/g, '').slice(0, 16)"
                                   placeholder="16 Digit NIK" 
                                   class="w-full text-xl bg-white/60 backdrop-blur-sm border border-white/80 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 rounded-[1.2rem] px-6 py-4 shadow-sm text-slate-800 placeholder-slate-400 font-medium transition-all" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 ml-2">Nama Lengkap Sesuai KTP</label>
                            <input type="text" v-model="form.name" placeholder="Nama Pasien" 
                                   class="w-full text-xl bg-white/60 backdrop-blur-sm border border-white/80 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 rounded-[1.2rem] px-6 py-4 shadow-sm text-slate-800 placeholder-slate-400 font-medium transition-all" required>
                        </div>
                        
                        <!-- PEMICU CUSTOM DATE PICKER -->
                        <div>
                            <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 ml-2">Tanggal Lahir</label>
                            <div @click="isDatePickerOpen = true"
                                 class="w-full bg-white/60 backdrop-blur-sm border border-white/80 rounded-[1.2rem] px-6 py-4 flex justify-between items-center cursor-pointer shadow-sm transition-all hover:bg-white/80">
                                <span :class="form.dob ? 'text-slate-800 font-bold text-xl' : 'text-slate-400 text-xl font-medium'">
                                    {{ form.dob ? formatDateDisplay(form.dob) : 'Ketuk untuk memilih tanggal' }}
                                </span>
                                <svg class="w-7 h-7 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                        </div>
                        
                        <button type="button" @click="nextStep" class="w-full mt-4 bg-slate-800 hover:bg-slate-700 text-white font-black text-xl py-5 rounded-[1.2rem] shadow-xl hover:shadow-2xl transition-all transform active:scale-95 tracking-wide">
                            Lanjut ke Kontak
                        </button>
                    </div>
                </transition>

                <!-- STEP 2: KONTAK & ALAMAT -->
                <transition enter-active-class="transition duration-500 ease-out" enter-from-class="opacity-0 translate-x-10" enter-to-class="opacity-100 translate-x-0" leave-active-class="hidden">
                    <div v-show="step === 2" class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 ml-2">Nomor WhatsApp Aktif</label>
                            <input type="tel" inputmode="numeric" v-model="form.phone" 
                                   @input="form.phone = form.phone.replace(/[^0-9]/g, '')"
                                   placeholder="Misal: 08123456789" 
                                   class="w-full text-xl bg-white/60 backdrop-blur-sm border border-white/80 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 rounded-[1.2rem] px-6 py-4 shadow-sm text-slate-800 placeholder-slate-400 font-medium transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 ml-2">Alamat Domisili Saat Ini</label>
                            <textarea v-model="form.address" rows="3" placeholder="Tuliskan alamat lengkap..." 
                                      class="w-full text-xl bg-white/60 backdrop-blur-sm border border-white/80 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 rounded-[1.2rem] px-6 py-4 shadow-sm text-slate-800 placeholder-slate-400 font-medium transition-all" required></textarea>
                        </div>
                        
                        <div class="flex gap-4 mt-4">
                            <button type="button" @click="prevStep" class="w-1/3 bg-white/80 border border-slate-200 text-slate-700 font-black text-xl py-5 rounded-[1.2rem] shadow-sm hover:bg-slate-50 transition-all active:scale-95">
                                Kembali
                            </button>
                            <button type="button" @click="nextStep" class="w-2/3 bg-slate-800 hover:bg-slate-700 text-white font-black text-xl py-5 rounded-[1.2rem] shadow-xl hover:shadow-2xl transition-all transform active:scale-95 tracking-wide">
                                Lanjut Layanan
                            </button>
                        </div>
                    </div>
                </transition>

                <!-- STEP 3: LAYANAN & ALERGI -->
                <transition enter-active-class="transition duration-500 ease-out" enter-from-class="opacity-0 translate-x-10" enter-to-class="opacity-100 translate-x-0" leave-active-class="hidden">
                    <div v-show="step === 3" class="space-y-6">
                        
                        <!-- KOTAK ALERGI PREMIUM -->
                        <div class="bg-gradient-to-br from-rose-50/90 to-red-50/50 backdrop-blur-sm p-6 rounded-[1.5rem] border border-rose-200/60 shadow-sm relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 opacity-5 text-red-500">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            </div>
                            <label class="block text-base font-black text-rose-700 mb-3 flex items-center gap-2 uppercase tracking-wide">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Riwayat Alergi (Wajib)
                            </label>
                            <textarea v-model="form.allergies" rows="2" placeholder="Ketik 'TIDAK ADA' jika aman..." 
                                      class="w-full text-xl bg-white/90 border-2 border-rose-200 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/20 rounded-[1.2rem] px-5 py-3 shadow-inner font-medium text-slate-800 placeholder-slate-400 transition-all relative z-10" required></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 ml-2">Pilih Poliklinik</label>
                            <select v-model="form.clinic_id" class="w-full text-xl bg-white/60 backdrop-blur-sm border border-white/80 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 rounded-[1.2rem] px-6 py-4 shadow-sm text-slate-800 font-medium transition-all appearance-none cursor-pointer" required>
                                <option value="" disabled selected>-- Ketuk untuk memilih --</option>
                                <option v-for="clinic in clinics" :key="clinic.id" :value="clinic.id">{{ clinic.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 ml-2">Pilih Dokter</label>
                            <select v-model="form.doctor_id" class="w-full text-xl bg-white/60 backdrop-blur-sm border border-white/80 focus:border-teal-500 focus:ring-4 focus:ring-teal-500/20 rounded-[1.2rem] px-6 py-4 shadow-sm text-slate-800 font-medium transition-all appearance-none cursor-pointer" required>
                                <option value="" disabled selected>-- Ketuk untuk memilih --</option>
                                <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">dr. {{ doctor.name }}</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-4 mt-4">
                            <button type="button" @click="prevStep" :disabled="form.processing" class="w-1/3 bg-white/80 border border-slate-200 text-slate-700 font-black text-xl py-5 rounded-[1.2rem] shadow-sm hover:bg-slate-50 transition-all active:scale-95 disabled:opacity-50">
                                Kembali
                            </button>
                            <button type="submit" :disabled="form.processing" class="w-2/3 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white font-black text-xl py-5 rounded-[1.2rem] shadow-xl hover:shadow-2xl transition-all transform active:scale-95 flex items-center justify-center gap-3 disabled:opacity-70 tracking-wide">
                                <svg v-if="form.processing" class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span v-if="!form.processing">Ambil Antrean</span>
                                <span v-else>Memproses...</span>
                            </button>
                        </div>
                    </div>
                </transition>

                <!-- STEP 4: SUKSES (TAMPILAN TIKET PREMIUM) -->
                <transition enter-active-class="transition duration-700 ease-out" enter-from-class="opacity-0 scale-90 translate-y-8" enter-to-class="opacity-100 scale-100 translate-y-0">
                    <div v-show="step === 4" class="text-center py-4">
                        <div class="w-24 h-24 bg-gradient-to-br from-emerald-100 to-teal-100 border border-emerald-200 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        
                        <h2 class="text-3xl font-black text-slate-800 mb-2 tracking-tight">Pendaftaran Sukses!</h2>
                        <p class="text-slate-500 text-lg font-medium mb-8">{{ page.props.flash.success }}</p>
                        
                        <!-- TIKET KIOSK -->
                        <div class="bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-[2rem] p-8 mb-8 inline-block shadow-2xl relative overflow-hidden text-left min-w-[320px] sm:min-w-[400px] border border-slate-700">
                            <!-- Watermark Card -->
                            <div class="absolute -right-10 -bottom-10 opacity-5">
                                <svg class="w-56 h-56" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" /><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" /></svg>
                            </div>
                            
                            <div class="relative z-10">
                                <div class="border-b border-slate-700 pb-5 mb-5">
                                    <p class="text-slate-400 text-xs font-black uppercase tracking-widest mb-1">Pasien Terdaftar</p>
                                    <p class="text-2xl font-black tracking-tight">{{ form.name }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-slate-400 text-xs font-black uppercase tracking-widest mb-1">Status Kunjungan</p>
                                    <div class="flex items-center gap-3">
                                        <span class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                        </span>
                                        <p class="text-emerald-400 text-lg font-bold tracking-wide">Menunggu Panggilan</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" @click="resetKiosk" class="w-full bg-white border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-800 font-black text-xl py-5 rounded-[1.2rem] shadow-sm transition-all transform active:scale-95 uppercase tracking-widest">
                            Selesai (Pasien Berikutnya)
                        </button>
                    </div>
                </transition>
            </form>
        </div>

        <!-- ========================================== -->
        <!-- MODAL OVERLAY: CUSTOM DATE PICKER KIOSK    -->
        <!-- ========================================== -->
        <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <!-- Wrapper Fixed yang menutupi seluruh layar -->
            <div v-if="isDatePickerOpen" class="fixed inset-0 z-[100] flex items-center justify-center">
                <!-- Background Blur Hitam Semi Transparan -->
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="isDatePickerOpen = false"></div>
                
                <!-- Panel Kalender (Di Tengah) -->
                <div class="relative bg-white/95 backdrop-blur-3xl border border-white/80 rounded-[2.5rem] shadow-[0_30px_60px_rgba(0,0,0,0.3)] p-8 sm:p-10 w-full max-w-lg mx-4 animate-fade-in-down">
                    <h3 class="text-center text-slate-800 font-bold text-xl mb-6 border-b border-slate-200 pb-4">Pilih Tanggal Lahir</h3>
                    
                    <div class="flex gap-4 mb-6">
                        <select v-model="currentMonth" class="w-1/2 bg-slate-50 border border-slate-200 rounded-2xl text-xl font-bold text-slate-700 py-4 px-6 focus:ring-teal-500 focus:border-teal-500 shadow-sm appearance-none cursor-pointer text-center">
                            <option v-for="(m, i) in monthNames" :key="i" :value="i">{{ m }}</option>
                        </select>
                        <select v-model="currentYear" class="w-1/2 bg-slate-50 border border-slate-200 rounded-2xl text-xl font-bold text-slate-700 py-4 px-6 focus:ring-teal-500 focus:border-teal-500 shadow-sm appearance-none cursor-pointer text-center">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-7 gap-2 mb-3">
                        <div v-for="d in days" :key="d" class="text-center text-sm font-black text-teal-600 uppercase tracking-wider">{{ d }}</div>
                    </div>
                    <div class="grid grid-cols-7 gap-2">
                        <div v-for="blank in blankDays" :key="'blank-'+blank" class="h-14"></div>
                        <div v-for="day in monthDays" :key="day" @click="selectDate(day)"
                             class="h-14 flex items-center justify-center text-xl font-bold rounded-2xl cursor-pointer transition-all duration-200 shadow-sm"
                             :class="(form.dob === `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`) 
                                ? 'bg-gradient-to-br from-teal-500 to-emerald-500 text-white shadow-lg transform scale-110' 
                                : 'bg-white border border-slate-100 text-slate-700 hover:bg-teal-50 active:scale-95'">
                            {{ day }}
                        </div>
                    </div>
                    
                    <button type="button" @click="isDatePickerOpen = false" class="w-full mt-8 bg-slate-200 text-slate-700 hover:bg-slate-300 font-bold text-lg py-4 rounded-2xl transition-colors">
                        Tutup Panel
                    </button>
                </div>
            </div>
        </transition>

    </div>
</template>

<style scoped>
/* KEYFRAMES UNTUK FLOATING ANIMATION */
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}
.animate-float {
    animation: float 6s ease-in-out infinite;
}
.animate-float-delayed {
    animation: float 7s ease-in-out infinite;
    animation-delay: 2s;
}
.animate-float-delayed-2 {
    animation: float 8s ease-in-out infinite;
    animation-delay: 4s;
}

/* Sembunyikan panah atas-bawah pada input angka HTML5 */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
input[type=number] {
  -moz-appearance: textfield;
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-30px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.animate-fade-in-down {
    animation: fadeInDown 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>