<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GlassButton from '@/Components/GlassButton.vue';
import GlassCheckbox from '@/Components/GlassCheckbox.vue';
import GlassInput from '@/Components/GlassInput.vue';
import GlassTextarea from '@/Components/GlassTextarea.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    clinics: Array,
    doctors: Array,
});

const form = useForm({
    name: '',
    nik: '',
    dob: '', 
    phone: '', // <-- DITAMBAHKAN
    address: '',
    allergies: '', // <-- DITAMBAHKAN
    clinic_id: '',
    doctor_id: '',
    is_vvip: false,
});

const isClinicDropdownOpen = ref(false);
const isDoctorDropdownOpen = ref(false);

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

// Toast State
const showToast = ref(false);
const toastType = ref('success');
const toastMessage = ref('');

const submit = () => {
    form.post(route('pendaftaran.store'), {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset(); 
            toastType.value = 'success';
            toastMessage.value = page.props.flash.success || 'Pasien berhasil didaftarkan!';
            showToast.value = true;
            setTimeout(() => { showToast.value = false; }, 5000);
        },
        onError: (errors) => {
            toastType.value = 'error';
            if (errors.error) {
                toastMessage.value = errors.error;
            } else {
                toastMessage.value = 'Pendaftaran ditolak! Silakan periksa kolom yang berwarna merah.';
            }
            showToast.value = true;
            setTimeout(() => { showToast.value = false; }, 7000);
        }
    });
};
</script>

<template>
    <Head title="Pendaftaran Pasien" />

    <AuthenticatedLayout class="min-h-screen overflow-x-hidden">
        
        <!-- KOMPONEN GLASSTOAST NOTIFIKASI (Jika Anda memiliki komponen GlassToast, letakkan di sini) -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-8"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
        </transition>

        <template #header>
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">Frontdesk <span class="text-indigo-600 font-light">| Registrasi Pasien Baru</span></h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white/50 backdrop-blur-xl border border-white/60 shadow-2xl rounded-3xl p-8 relative overflow-visible">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-indigo-200 to-transparent opacity-40 rounded-bl-full pointer-events-none"></div>

                    <form @submit.prevent="submit" class="relative z-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                            
                            <!-- KOLOM KIRI: Data Diri -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" /></svg>
                                    Informasi Personal
                                </h3>

                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Nama Lengkap Pasien</label>
                                     <GlassInput v-model="form.name" @input="form.clearErrors('name')" placeholder="Sesuai KTP..." :error="Boolean(form.errors.name)" />
                                    <p v-if="form.errors.name" class="text-red-500 text-xs font-bold mt-1.5">{{ form.errors.name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Nomor Induk Kependudukan (NIK)</label>
                                     <GlassInput type="text" inputmode="numeric" v-model="form.nik"
                                         @input="form.nik = form.nik.replace(/[^0-9]/g, '').slice(0, 16); form.clearErrors('nik')"
                                         placeholder="Wajib 16 Digit Angka" :error="Boolean(form.errors.nik)" />
                                    <p v-if="form.errors.nik" class="text-red-500 text-xs font-bold mt-1.5">{{ form.errors.nik }}</p>
                                    <p v-else class="text-slate-400 text-[10px] font-bold mt-1 text-right">{{ form.nik.length }}/16 Digit</p>
                                </div>

                                <!-- NOMOR HP DITAMBAHKAN DI SINI -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Nomor Handphone</label>
                                     <GlassInput type="text" inputmode="tel" v-model="form.phone"
                                         @input="form.phone = form.phone.replace(/[^0-9+]/g, ''); form.clearErrors('phone')"
                                         placeholder="Contoh: 08123456789" :error="Boolean(form.errors.phone)" />
                                    <p v-if="form.errors.phone" class="text-red-500 text-xs font-bold mt-1.5">{{ form.errors.phone }}</p>
                                </div>

                                <!-- CUSTOM DATE PICKER -->
                                <div class="relative">
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                                    <div @click="isDatePickerOpen = !isDatePickerOpen"
                                         class="w-full bg-white/40 backdrop-blur-sm border border-white/60 rounded-xl px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-white/60 transition-all shadow-sm"
                                         :class="{'ring-2 ring-indigo-400 bg-white/70': isDatePickerOpen, 'border-red-400 bg-red-50/50': form.errors.dob}">
                                        <span :class="form.dob ? 'text-slate-800 font-bold' : 'text-slate-400 font-medium'">
                                            {{ form.dob ? formatDateDisplay(form.dob) : 'Pilih Tanggal...' }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="{'rotate-12': isDatePickerOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <p v-if="form.errors.dob" class="text-red-500 text-xs font-bold mt-1.5">{{ form.errors.dob }}</p>

                                    <!-- Panel Kalender -->
                                    <div v-if="isDatePickerOpen" class="absolute left-0 z-50 mt-2 w-80 bg-white/80 backdrop-blur-2xl border border-white/80 rounded-2xl shadow-2xl p-4 animate-fade-in-down">
                                        <div class="flex gap-2 mb-4">
                                            <select v-model="currentMonth" class="w-1/2 bg-white/60 border border-slate-200 rounded-lg text-sm font-bold text-slate-700 py-1.5 focus:ring-indigo-400 focus:border-indigo-400">
                                                <option v-for="(m, i) in monthNames" :key="i" :value="i">{{ m }}</option>
                                            </select>
                                            <select v-model="currentYear" class="w-1/2 bg-white/60 border border-slate-200 rounded-lg text-sm font-bold text-slate-700 py-1.5 focus:ring-indigo-400 focus:border-indigo-400">
                                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                            </select>
                                        </div>
                                        <div class="grid grid-cols-7 gap-1 mb-2">
                                            <div v-for="d in days" :key="d" class="text-center text-[11px] font-bold text-indigo-400 uppercase tracking-wider">{{ d }}</div>
                                        </div>
                                        <div class="grid grid-cols-7 gap-1">
                                            <div v-for="blank in blankDays" :key="'blank-'+blank" class="h-8"></div>
                                            <div v-for="day in monthDays" :key="day" @click="selectDate(day)"
                                                 class="h-8 flex items-center justify-center text-sm font-bold rounded-lg cursor-pointer transition-all duration-200"
                                                 :class="(form.dob === `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`) 
                                                    ? 'bg-gradient-to-br from-indigo-500 to-blue-500 text-white shadow-md transform scale-110' 
                                                    : 'text-slate-700 hover:bg-indigo-100 hover:text-indigo-700'">
                                                {{ day }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="isDatePickerOpen" @click="isDatePickerOpen = false" class="fixed inset-0 z-40"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Alamat Tempat Tinggal</label>
                                    <GlassTextarea v-model="form.address" @input="form.clearErrors('address')" rows="3" placeholder="Alamat lengkap..." :error="Boolean(form.errors.address)" />
                                    <p v-if="form.errors.address" class="text-red-500 text-xs font-bold mt-1.5">{{ form.errors.address }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Riwayat Alergi (Opsional)</label>
                                    <GlassTextarea v-model="form.allergies" @input="form.clearErrors('allergies')" rows="2" placeholder="Contoh: Alergi seafood, paracetamol..." :error="Boolean(form.errors.allergies)" />
                                    <p v-if="form.errors.allergies" class="text-red-500 text-xs font-bold mt-1.5">{{ form.errors.allergies }}</p>
                                    <p v-else class="text-slate-400 text-[10px] font-bold mt-1">Kosongkan jika pasien tidak memiliki riwayat alergi yang diketahui.</p>
                                </div>
                            </div>

                            <!-- KOLOM KANAN: Data Kunjungan -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2 mb-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                                    Registrasi Layanan
                                </h3>

                                <div class="relative">
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Poliklinik Tujuan</label>
                                    <div @click="isClinicDropdownOpen = !isClinicDropdownOpen" 
                                         class="w-full bg-white/40 backdrop-blur-sm border border-white/60 rounded-xl px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-white/60 transition-all shadow-sm"
                                         :class="{'ring-2 ring-indigo-400 bg-white/70': isClinicDropdownOpen, 'border-red-400 bg-red-50/50': form.errors.clinic_id}">
                                        <span :class="form.clinic_id ? 'text-slate-800 font-bold' : 'text-slate-400 font-medium'">
                                            {{ form.clinic_id ? clinics.find(c => c.id === form.clinic_id)?.name : '-- Pilih Poliklinik --' }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="{'rotate-180': isClinicDropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                    <p v-if="form.errors.clinic_id" class="text-red-500 text-xs font-bold mt-1.5">Silakan pilih poliklinik tujuan.</p>
                                    
                                    <div v-if="isClinicDropdownOpen" class="absolute z-50 w-full mt-2 bg-white/80 backdrop-blur-xl border border-white/80 rounded-2xl shadow-2xl overflow-hidden animate-fade-in-down">
                                        <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                            <div v-for="clinic in clinics" :key="clinic.id" 
                                                 @click="form.clinic_id = clinic.id; isClinicDropdownOpen = false; form.clearErrors('clinic_id')"
                                                 class="px-4 py-3 hover:bg-indigo-500/10 hover:text-indigo-700 cursor-pointer rounded-xl text-slate-700 font-medium transition-colors flex items-center gap-2"
                                                 :class="{'bg-indigo-50 text-indigo-700 font-bold': form.clinic_id === clinic.id}">
                                                <div class="w-2 h-2 rounded-full" :class="form.clinic_id === clinic.id ? 'bg-indigo-500' : 'bg-transparent'"></div>
                                                {{ clinic.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="isClinicDropdownOpen" @click="isClinicDropdownOpen = false" class="fixed inset-0 z-40"></div>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Dokter Pemeriksa</label>
                                    <div @click="isDoctorDropdownOpen = !isDoctorDropdownOpen" 
                                         class="w-full bg-white/40 backdrop-blur-sm border border-white/60 rounded-xl px-4 py-3 flex justify-between items-center cursor-pointer hover:bg-white/60 transition-all shadow-sm"
                                         :class="{'ring-2 ring-indigo-400 bg-white/70': isDoctorDropdownOpen, 'border-red-400 bg-red-50/50': form.errors.doctor_id}">
                                        <span :class="form.doctor_id ? 'text-slate-800 font-bold' : 'text-slate-400 font-medium'">
                                            {{ form.doctor_id ? `dr. ${doctors.find(d => d.id === form.doctor_id)?.name}` : '-- Pilih Dokter --' }}
                                        </span>
                                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200" :class="{'rotate-180': isDoctorDropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                    <p v-if="form.errors.doctor_id" class="text-red-500 text-xs font-bold mt-1.5">Silakan pilih dokter pemeriksa.</p>
                                    
                                    <div v-if="isDoctorDropdownOpen" class="absolute z-50 w-full mt-2 bg-white/80 backdrop-blur-xl border border-white/80 rounded-2xl shadow-2xl overflow-hidden animate-fade-in-down">
                                        <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                            <div v-for="doctor in doctors" :key="doctor.id" 
                                                 @click="form.doctor_id = doctor.id; isDoctorDropdownOpen = false; form.clearErrors('doctor_id')"
                                                 class="px-4 py-3 hover:bg-indigo-500/10 hover:text-indigo-700 cursor-pointer rounded-xl text-slate-700 font-medium transition-colors flex items-center gap-2"
                                                 :class="{'bg-indigo-50 text-indigo-700 font-bold': form.doctor_id === doctor.id}">
                                                <div class="w-2 h-2 rounded-full" :class="form.doctor_id === doctor.id ? 'bg-indigo-500' : 'bg-transparent'"></div>
                                                dr. {{ doctor.name }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="isDoctorDropdownOpen" @click="isDoctorDropdownOpen = false" class="fixed inset-0 z-40"></div>
                                </div>

                                <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 backdrop-blur-sm mt-4">
                                    <label class="flex items-center cursor-pointer group">
                                        <GlassCheckbox v-model="form.is_vvip" class="group-hover:scale-110" />
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-slate-700 group-hover:text-indigo-700 transition-colors">Tandai sebagai Pasien VIP</span>
                                            <span class="block text-xs text-slate-500">Memberikan prioritas antrean pada dashboard dokter.</span>
                                        </div>
                                    </label>
                                </div>

                                <div class="pt-6 mt-auto">
                                        <GlassButton type="submit" :disabled="form.processing"
                                            class="w-full py-4 px-8 rounded-2xl flex justify-center items-center gap-2">
                                        <svg v-if="form.processing" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938ll3-2.647z"></path></svg>
                                        {{ form.processing ? 'Menyimpan...' : 'Daftarkan & Buat Antrean' }}
                                        </GlassButton>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-down {
    animation: fadeInDown 0.2s ease-out forwards;
}
</style>