<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-2xl text-slate-800 leading-tight">Pengaturan Profil</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Box 1: Update Profile -->
                <div class="p-4 sm:p-8 bg-white/50 backdrop-blur-xl border border-white/60 shadow-xl rounded-3xl">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- Box 2: Update Password -->
                <div class="p-4 sm:p-8 bg-white/50 backdrop-blur-xl border border-white/60 shadow-xl rounded-3xl">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- Box 3: Delete Account -->
                <div class="p-4 sm:p-8 bg-red-50/40 backdrop-blur-xl border border-red-100 shadow-xl rounded-3xl">
                    <DeleteUserForm class="max-w-xl" />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>