<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <!-- Background Abstract (Ornamen Latar Belakang) -->
    <div class="fixed inset-0 z-0 pointer-events-none bg-slate-50 overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] rounded-full bg-blue-300/30 blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[35vw] h-[35vw] rounded-full bg-teal-300/30 blur-[100px]"></div>
        <div class="absolute top-[30%] right-[15%] w-[25vw] h-[25vw] rounded-full bg-indigo-300/20 blur-[100px]"></div>
    </div>

    <div class="min-h-screen relative z-10 flex flex-col">
        <!-- Navbar Glassmorphism -->
        <nav class="bg-white/60 backdrop-blur-xl border-b border-white/50 shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-teal-400 rounded-xl flex items-center justify-center shadow-lg">
                                    <span class="text-white font-bold text-xl">N</span>
                                </div>
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard Antrean
                            </NavLink>
                            <!-- Tambahkan Link Farmasi Jika Role Sesuai (Opsional untuk nanti) -->
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <!-- Settings Dropdown -->
                        <div class="ms-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-slate-700 bg-white/50 hover:bg-white/80 focus:outline-none transition ease-in-out duration-150 shadow-sm backdrop-blur-md"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="ms-2 -me-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading (Juga dengan efek transparan) -->
        <header class="bg-white/40 backdrop-blur-md border-b border-white/30 shadow-sm" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-grow">
            <slot />
        </main>
    </div>
</template>