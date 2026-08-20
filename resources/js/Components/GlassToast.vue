<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success'); // 'success' atau 'error'

// Fungsi untuk memicu toast
const triggerToast = () => {
    if (page.props.flash.success) {
        message.value = page.props.flash.success;
        type.value = 'success';
        show.value = true;
    } else if (page.props.flash.error) {
        message.value = page.props.flash.error;
        type.value = 'error';
        show.value = true;
    }

    // Auto-hide setelah 5 detik
    if (show.value) {
        setTimeout(() => {
            show.value = false;
        }, 5000);
    }
};

// Pantau perubahan flash message (penting untuk SPA Inertia)
watch(() => page.props.flash, () => {
    triggerToast();
}, { deep: true });

// Cek saat pertama kali dimuat
onMounted(() => {
    triggerToast();
});
</script>

<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-8"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed top-24 right-8 z-[100] max-w-md w-full bg-white/70 backdrop-blur-xl border border-white/80 shadow-2xl rounded-2xl p-4 flex items-center gap-4">
            
            <!-- Ikon Dinamis (Hijau untuk sukses, Merah untuk error) -->
            <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0 shadow-inner"
                 :class="type === 'success' ? 'bg-emerald-100' : 'bg-rose-100'">
                <svg v-if="type === 'success'" class="w-7 h-7 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <svg v-else class="w-7 h-7 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Teks Dinamis -->
            <div>
                <h4 class="text-sm font-bold text-slate-800">
                    {{ type === 'success' ? 'Notifikasi Sistem' : 'Peringatan Sistem' }}
                </h4>
                <p class="text-sm mt-0.5 leading-tight" :class="type === 'success' ? 'text-slate-600' : 'text-rose-600 font-medium'">
                    {{ message }}
                </p>
            </div>

            <!-- Tombol Tutup -->
            <button @click="show = false" class="ml-auto text-slate-400 hover:text-slate-600 transition-colors bg-white/50 p-1.5 rounded-full hover:bg-white/80">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </transition>
</template>