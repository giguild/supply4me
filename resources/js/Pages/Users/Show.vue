<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="`User: ${user.name}`" subtitle="User profile and role information">
            <template #actions>
                <Link :href="route('users.index')" class="btn btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </Link>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4">User Details</h3>
                <div class="flex items-start gap-6">
                    <div class="relative group cursor-pointer shrink-0" @click="$refs.adminAvatarInput.click()">
                        <div v-if="userAvatar" class="w-20 h-20 rounded-full overflow-hidden border-2 border-gray-200 dark:border-gray-600">
                            <img :src="userAvatar" class="w-full h-full object-cover" alt="Avatar" />
                        </div>
                        <div v-else class="w-20 h-20 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold text-2xl border-2 border-gray-200 dark:border-gray-600">
                            {{ user.name?.charAt(0) || '?' }}
                        </div>
                        <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <input ref="adminAvatarInput" type="file" accept="image/*" class="hidden" @change="uploadAvatar" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                        <p class="font-medium">{{ user.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="font-medium">{{ user.email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="font-medium">{{ user.phone || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <StatusBadge :value="user.status" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Company</p>
                        <p class="font-medium">{{ user.company?.name || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Branch</p>
                        <p class="font-medium">{{ user.branches?.map(b => b.name).join(', ') || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
                        <p class="font-medium">{{ new Date(user.created_at).toLocaleDateString('en-NG', { year: 'numeric', month: 'short', day: 'numeric' }) }}</p>
                    </div>
                </div>
                </div>
            </div>

            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4">Role</h3>
                <div class="space-y-4">
                    <div v-for="role in user.roles" :key="role.id">
                        <StatusBadge :value="role.name" :variant="role.name === 'admin' ? 'purple' : role.name === 'manager' ? 'info' : 'success'" />
                    </div>
                    <p v-if="!user.roles?.length" class="text-gray-500 dark:text-gray-400 text-sm">No roles assigned</p>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <Link :href="route('users.edit', user.id)" class="btn btn-primary w-full">
                        Edit User
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

const props = defineProps({
    user: Object,
});

const adminAvatarInput = ref(null);

const userAvatar = computed(() => {
    return props.user?.avatar ? `/storage/${props.user.avatar}` : null
});

function uploadAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const form = useForm({ avatar: file });
    form.post(route('users.avatar', props.user.id), {
        onFinish: () => { if (adminAvatarInput.value) adminAvatarInput.value.value = ''; }
    });
}
</script>
