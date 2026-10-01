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
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-slate-100">
                        Pengaturan Profil & Akun
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Kelola identitas diri dan kata sandi akun Anda</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="bg-orbit-surface border border-orbit-border rounded-3xl p-6 sm:p-8 shadow-xl">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-orbit-surface border border-orbit-border rounded-3xl p-6 sm:p-8 shadow-xl">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-orbit-surface border border-orbit-border rounded-3xl p-6 sm:p-8 shadow-xl">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
