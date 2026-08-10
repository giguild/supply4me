<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="Stock Overview">
                <template #actions>
                    <Link :href="route('stock.adjustments.index')" class="btn btn-outline btn-sm">Adjustments</Link>
                    <Link :href="route('stock.transfers.index')" class="btn btn-accent btn-sm">Transfers</Link>
                </template>
            </PageHeader>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <StatCard label="Total Items" :value="stats.total_items" />
                <StatCard label="Low Stock" :value="stats.low_stock" />
                <StatCard label="Out of Stock" :value="stats.out_of_stock" />
                <StatCard label="Total Value" :value="stats.total_value" prefix="$" />
            </div>

            <div class="card p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Warehouse</label>
                        <select v-model="warehouseFilter" class="form-input" @change="applyFilters">
                            <option value="">All Warehouses</option>
                            <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{ wh.name }}</option>
                        </select>
                    </div>
                    <SearchInput v-model="search" placeholder="Search products..." />
                </div>
            </div>

            <DataTable
                :columns="columns"
                :data="stockItems.data"
                :meta="stockItems"
                @page="(p) => router.get(route('stock.index'), { ...filters, page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-product="{ row }">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.product?.name }}</span>
                </template>
                <template #cell-warehouse="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.warehouse?.name }}</span>
                </template>
                <template #cell-quantity_on_hand="{ row }">
                    {{ row.quantity_on_hand }}
                </template>
                <template #cell-quantity_reserved="{ row }">
                    {{ row.quantity_reserved }}
                </template>
                <template #cell-reorder_level="{ row }">
                    {{ row.reorder_level }}
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge
                        v-if="row.quantity_on_hand === 0"
                        value="out_of_stock"
                        label="Out of Stock"
                    />
                    <StatusBadge
                        v-else-if="row.quantity_on_hand < row.reorder_level"
                        value="low_stock"
                        label="Low Stock"
                    />
                    <StatusBadge v-else value="active" label="In Stock" />
                </template>
                <template #cell-last_counted_at="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.last_counted_at ?? 'Never' }}</span>
                </template>

                <template #empty>
                    <EmptyState title="No stock items found" description="Stock items will appear here once products are added to warehouses.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </template>
                        <template #action>
                            <Link :href="route('stock.transfers.create')" class="btn btn-accent">New Transfer</Link>
                        </template>
                    </EmptyState>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import SearchInput from '@/Components/UI/SearchInput.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';

const props = defineProps({
    stockItems: Object,
    warehouses: Array,
    stats: { type: Object, default: () => ({ total_items: 0, low_stock: 0, out_of_stock: 0, total_value: 0 }) },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search || '');
const warehouseFilter = ref(props.filters.warehouse_id || '');

const columns = [
    { key: 'product', label: 'Product' },
    { key: 'warehouse', label: 'Warehouse' },
    { key: 'quantity_on_hand', label: 'Qty On Hand' },
    { key: 'quantity_reserved', label: 'Qty Reserved' },
    { key: 'reorder_level', label: 'Reorder Level' },
    { key: 'status', label: 'Status' },
    { key: 'last_counted_at', label: 'Last Counted' },
];

let debounceTimer = null;
const debouncedSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(applyFilters, 300);
};

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (warehouseFilter.value) params.warehouse_id = warehouseFilter.value;
    router.get(route('stock.index'), params, { preserveState: true, replace: true });
};
</script>
