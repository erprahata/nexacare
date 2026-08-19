<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50 relative overflow-hidden">
        
        <!-- Background Abstract (Ornamen) -->
        <div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] rounded-full bg-blue-300/30 blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[35vw] h-[35vw] rounded-full bg-teal-300/30 blur-[100px] pointer-events-none"></div>
        <div class="absolute top-[40%] right-[20%] w-[20vw] h-[20vw] rounded-full bg-indigo-300/20 blur-[100px] pointer-events-none"></div>

        <!-- Logo -->
        <div class="relative z-10 mb-6 text-center">
            <Link href="/">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-teal-400 rounded-2xl flex items-center justify-center shadow-xl mx-auto transform transition hover:scale-105">
                    <span class="text-white font-bold text-4xl">N</span>
                </div>
            </Link>
            <h2 class="mt-4 text-2xl font-extrabold text-slate-800 tracking-tight">Nexa<span class="text-teal-600">Care</span></h2>
            <p class="text-sm text-slate-500 font-medium">Sistem Informasi Manajemen Cerdas</p>
        </div>

        <!-- Form Container (Glassmorphism) -->
        <div class="w-full sm:max-w-md mt-2 px-8 py-10 bg-white/60 backdrop-blur-xl border border-white/60 shadow-2xl overflow-hidden sm:rounded-3xl relative z-10">
            <slot />
        </div>
        
    </div>
</template>