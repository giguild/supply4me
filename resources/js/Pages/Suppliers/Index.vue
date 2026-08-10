<template>
    <AppLayout :user="$page.props.auth.user">
            <PageHeader title="Suppliers">
                <template #actions>
                    <Link :href="route('suppliers.create')" class="btn btn-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Supplier
                    </Link>
                </template>
            </PageHeader>

            <!-- Filters -->
            <div class="card p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <SearchInput v-model="search" />
                    </div>
                    <div>
                        <select
                            v-model="filterStatus"
                            class="form-input"
                            @change="applyFilters"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <DataTable
                :columns="columns"
                :data="suppliers.data"
                :meta="suppliers"
                @page="goToPage"
                @rowClick="goToShow"
            >
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" />
                </template>

                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-2">
                        <Link :href="route('suppliers.show', row.id)" class="btn btn-outline btn-sm">View</Link>
                        <Link :href="route('suppliers.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                        <button @click="confirmDelete(row)" class="btn btn-danger btn-sm">Delete</button>
                    </div>
                </template>

                <template #empty>
                    <EmptyState title="No suppliers found" description="Get started by adding your first supplier.">
                        <template #action>
                            <Link :href="route('suppliers.create')" class="btn btn-accent">Add Supplier</Link>
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
    suppliers: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || '');

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'phone', label: 'Phone' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const applyFilters = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('suppliers.index'), {
            search: search.value,
            status: filterStatus.value,
        }, { preserveState: true, replace: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('suppliers.index'), {
        page,
        search: search.value,
        status: filterStatus.value,
    }, { preserveState: true, replace: true });
};

const goToShow = (row) => {
    router.visit(route('suppliers.show', row.id));
};

const confirmDelete = (supplier) => {
    if (confirm(`Are you sure you want to delete "${supplier.name}"?`)) {
        router.delete(route('suppliers.destroy', supplier.id), {
            onSuccess: () => toast.success('Supplier deleted successfully.'),
        });
    }
};
</script>
