<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Drivers" subtitle="Manage your delivery drivers">
            <template #actions>
                <Link :href="route('drivers.create')" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Driver
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
            :data="drivers.data"
            :meta="drivers.meta"
            @page="goToPage"
        >
            <template #cell-status="{ value }">
                <StatusBadge :value="value" />
            </template>

            <template #cell-vehicle="{ row }">
                <span class="text-gray-600 dark:text-gray-400">{{ row.vehicle_type }} - {{ row.vehicle_registration }}</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link :href="route('drivers.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                    <button class="btn btn-danger btn-sm" @click="confirmDelete(row)">Delete</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No drivers found" description="Add your first driver to get started.">
                    <template #action>
                        <Link :href="route('drivers.create')" class="btn btn-primary">New Driver</Link>
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
    drivers: Object,
});

const toast = useToast();
const search = ref('');

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'phone', label: 'Phone' },
    { key: 'vehicle', label: 'Vehicle' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('drivers.index'), { search: search.value }, { preserveState: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('drivers.index'), { page, search: search.value }, { preserveState: true });
};

const confirmDelete = (driver) => {
    if (confirm(`Are you sure you want to delete "${driver.name}"?`)) {
        router.delete(route('drivers.destroy', driver.id), {
            onSuccess: () => {
                toast.success('Driver deleted successfully.');
            },
        });
    }
};
</script>
