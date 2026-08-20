<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    modelValue: [String, Number, null],
    options: {
        type: Array,
        required: true,
        // Format wajib: [{ id: 1, label: 'Teks' }]
    },
    placeholder: {
        type: String,
        default: '-- Pilih Opsi --'
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const selectRef = ref(null);
const searchInput = ref(null);
const searchQuery = ref('');

// Filter otomatis berdasarkan input pencarian
const filteredOptions = computed(() => {
    if (!searchQuery.value) return props.options;
    const query = searchQuery.value.toLowerCase();
    return props.options.filter(o => o.label.toLowerCase().includes(query));
});

const toggle = () => isOpen.value = !isOpen.value;

const selectOption = (option) => {
    emit('update:modelValue', option.id);
    isOpen.value = false;
};

// Tutup dropdown jika klik di luar
const closeDropdown = (e) => {
    if (selectRef.value && !selectRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

// Reset pencarian & fokus otomatis saat dropdown dibuka
watch(isOpen, (newVal) => {
    if (newVal) {
        setTimeout(() => {
            if (searchInput.value) searchInput.value.focus();
        }, 50);
    } else {
        searchQuery.value = '';
    }
});

onMounted(() => document.addEventListener('click', closeDropdown));
onUnmounted(() => document.removeEventListener('click', closeDropdown));
</script>

<template>
    <div class="relative" ref="selectRef">
        <!-- Kotak Pemicu (Pengganti Select Bawaan) -->
        <div @click="toggle"
             class="w-full text-sm bg-white/60 border border-white/50 focus:border-teal-500 rounded-xl shadow-sm backdrop-blur-md px-4 py-2.5 cursor-pointer flex justify-between items-center transition hover:bg-white/80"
             :class="{'ring-2 ring-teal-500': isOpen}">
            <span class="text-slate-700 truncate font-medium">
                {{ options.find(o => o.id === modelValue)?.label || placeholder }}
            </span>
            <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="{'rotate-180': isOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Menu Melayang (Glassmorphism) -->
        <transition 
            enter-active-class="transition ease-out duration-150" 
            enter-from-class="transform opacity-0 -translate-y-2" 
            enter-to-class="transform opacity-100 translate-y-0" 
            leave-active-class="transition ease-in duration-100" 
            leave-from-class="transform opacity-100 translate-y-0" 
            leave-to-class="transform opacity-0 -translate-y-2">
            
            <div v-if="isOpen" class="absolute z-50 w-full mt-2 bg-white/90 backdrop-blur-2xl border border-white/60 shadow-2xl rounded-xl overflow-hidden focus:outline-none flex flex-col max-h-72">
                
                <!-- Kotak Pencarian -->
                <div class="p-2 border-b border-slate-200/60 sticky top-0 bg-white/50 backdrop-blur-md z-10">
                    <input 
                        type="text" 
                        ref="searchInput"
                        v-model="searchQuery" 
                        placeholder="Ketik untuk mencari..." 
                        class="w-full text-sm bg-white/50 border-none focus:ring-0 rounded-lg px-3 py-1.5 text-slate-700 placeholder-slate-400"
                    />
                </div>

                <!-- Daftar Opsi -->
                <ul class="overflow-y-auto py-1">
                    <li v-for="option in filteredOptions" :key="option.id"
                        @click="selectOption(option)"
                        class="px-4 py-2.5 text-sm text-slate-700 hover:bg-gradient-to-r hover:from-teal-500 hover:to-emerald-400 hover:text-white cursor-pointer transition-all"
                        :class="{'bg-teal-50 text-teal-700 font-bold': option.id === modelValue}">
                        {{ option.label }}
                    </li>
                    <li v-if="filteredOptions.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center italic">
                        Obat tidak ditemukan
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>