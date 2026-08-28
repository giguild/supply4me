<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Warehouses">
            <template #actions>
                <Link :href="route('warehouses.create')" class="btn btn-accent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Warehouse
                </Link>
            </template>
        </PageHeader>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
            <div class="flex-1 max-w-md">
                <SearchInput v-model="search" />
            </div>
            <div>
                <select v-model="filterStatus" class="form-input" @change="applyFilters">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="warehouses.data"
            :meta="warehouses"
            :mobileColumns="mobileColumns"
            @page="goToPage"
            @rowClick="goToShow"
        >
            <template #cell-status="{ value }">
                <StatusBadge :value="value" />
            </template>

            <template #cell-type="{ value }">
                <span class="capitalize">{{ value }}</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('warehouses.show', row.id)" class="btn btn-outline btn-sm">View</Link>
                    <Link :href="route('warehouses.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                    <button @click="confirmDelete(row)" class="btn btn-danger btn-sm">Delete</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No warehouses found" description="Get started by adding your first warehouse.">
                    <template #action>
                        <Link :href="route('warehouses.create')" class="btn btn-accent">New Warehouse</Link>
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

const toast = useToast();

const props = defineProps({
    warehouses: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || '');

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'code', label: 'Code' },
    { key: 'branch_name', label: 'Branch' },
    { key: 'type', label: 'Type' },
    { key: 'capacity', label: 'Capacity' },
    { key: 'status', label: 'Status' },
];

const mobileColumns = [
    { key: 'name', label: 'Name' },
    { key: 'code', label: 'Code' },
    { key: 'type', label: 'Type' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const applyFilters = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('warehouses.index'), {
            search: search.value,
            status: filterStatus.value,
        }, { preserveState: true, replace: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('warehouses.index'), {
        page,
        search: search.value,
        status: filterStatus.value,
    }, { preserveState: true, replace: true });
};

const goToShow = (row) => {
    router.visit(route('warehouses.show', row.id));
};

const confirmDelete = (warehouse) => {
    if (confirm(`Are you sure you want to delete "${warehouse.name}"?`)) {
        router.delete(route('warehouses.destroy', warehouse.id), {
            onSuccess: () => toast.success('Warehouse deleted successfully.'),
        });
    }
};
</script>
