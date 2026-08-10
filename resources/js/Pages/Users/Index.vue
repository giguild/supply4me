<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Users" subtitle="Manage system users and their roles">
            <template #actions>
                <Link :href="route('users.create')" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New User
                </Link>
            </template>
        </PageHeader>

        <div class="flex items-center gap-4 mb-6">
            <div class="flex-1 max-w-md">
                <SearchInput v-model="search" />
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="users.data"
            :meta="users.meta"
            @page="goToPage"
        >
            <template #cell-status="{ value }">
                <StatusBadge :value="value" />
            </template>

            <template #cell-role="{ row }">
                <StatusBadge :value="row.roles?.[0]?.name" :variant="row.roles?.[0]?.name === 'admin' ? 'purple' : row.roles?.[0]?.name === 'manager' ? 'info' : 'success'" />
            </template>

            <template #cell-name="{ row }">
                <Link :href="route('users.show', row.id)" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent">
                    {{ row.name }}
                </Link>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link :href="route('users.show', row.id)" class="btn btn-outline btn-sm">View</Link>
                    <Link :href="route('users.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                    <button class="btn btn-danger btn-sm" @click="confirmDelete(row)">Delete</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No users found" description="Add your first user to get started.">
                    <template #action>
                        <Link :href="route('users.create')" class="btn btn-primary">New User</Link>
                    </template>
                </EmptyState>
            </template>
        </DataTable>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import SearchInput from '@/Components/UI/SearchInput.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    users: Object,
});

const toast = useToast();
const search = ref('');

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'role', label: 'Role' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('users.index'), { search: search.value }, { preserveState: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('users.index'), { page, search: search.value }, { preserveState: true });
};

const confirmDelete = (user) => {
    if (confirm(`Are you sure you want to delete "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id), {
            onSuccess: () => {
                toast.success('User deleted successfully.');
            },
        });
    }
};
</script>
