<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="Goods Received Notes">
                <template #actions>
                    <Link :href="route('grn.create')" class="btn btn-accent btn-sm">New GRN</Link>
                </template>
            </PageHeader>

            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
                <div class="flex-1 max-w-md">
                    <SearchInput v-model="search" @input="debouncedFetch" />
                </div>
                <select v-model="statusFilter" class="form-input w-full sm:w-48" @change="fetchGrns">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="in_progress">In Progress</option>
                    <option value="received">Received</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <DataTable
                :columns="columns"
                :mobileColumns="mobileColumns"
                :data="grns.data"
                :meta="meta"
                @rowClick="(row) => router.get(route('grn.show', row.id))"
                @page="goToPage"
            >
                <template #cell-grn_number="{ row }">
                    <span class="font-medium text-accent">{{ row.grn_number }}</span>
                </template>
                <template #cell-supplier="{ row }">
                    <span class="text-gray-900 dark:text-gray-100">{{ row.supplier?.name }}</span>
                </template>
                <template #cell-warehouse="{ row }">
                    <span class="text-gray-900 dark:text-gray-100">{{ row.warehouse?.name }}</span>
                </template>
                <template #cell-receiving_date="{ row }">
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ row.receiving_date }}</span>
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" :label="row.status?.replace('_', ' ')" />
                </template>
                <template #cell-items_count="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.items_count ?? row.items?.length ?? 0 }}</span>
                </template>

                <template #actions="{ row }">
                    <Link :href="route('grn.show', row.id)" class="btn btn-outline btn-sm">View</Link>
                </template>

                <template #empty>
                    <EmptyState title="No goods received notes" description="Create a GRN to record received goods from suppliers.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                        <template #action>
                            <Link :href="route('grn.create')" class="btn btn-accent">New GRN</Link>
                        </template>
                    </EmptyState>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import SearchInput from '@/Components/UI/SearchInput.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';

const props = defineProps({
    grns: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const columns = [
    { key: 'grn_number', label: 'GRN#' },
    { key: 'supplier', label: 'Supplier' },
    { key: 'warehouse', label: 'Warehouse' },
    { key: 'receiving_date', label: 'Date' },
    { key: 'status', label: 'Status' },
    { key: 'items_count', label: 'Items' },
];

const mobileColumns = [
    { key: 'grn_number', label: 'GRN#' },
    { key: 'supplier', label: 'Supplier' },
    { key: 'status', label: 'Status' },
];

const meta = computed(() => ({
    current_page: props.grns.current_page,
    last_page: props.grns.last_page,
    from: props.grns.from,
    to: props.grns.to,
    total: props.grns.total,
}));

let debounceTimer = null;
const debouncedFetch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchGrns, 300);
};

const fetchGrns = () => {
    router.get(route('grn.index'), {
        search: search.value,
        status: statusFilter.value,
    }, { preserveState: true, replace: true });
};

const goToPage = (page) => {
    router.get(route('grn.index'), {
        search: search.value,
        status: statusFilter.value,
        page,
    }, { preserveState: true, replace: true });
};
</script>
