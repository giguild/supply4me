<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="Stock Transfers">
                <template #actions>
                    <Link :href="route('stock.transfers.create')" class="btn btn-accent btn-sm">New Transfer</Link>
                </template>
            </PageHeader>

            <DataTable
                :columns="columns"
                :data="transfers.data"
                :meta="transfers"
                @page="(p) => router.get(route('stock.transfers.index'), { page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-created_at="{ row }">
                    {{ row.created_at }}
                </template>
                <template #cell-product="{ row }">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.product?.name }}</span>
                </template>
                <template #cell-from_warehouse="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.from_warehouse?.name }}</span>
                </template>
                <template #cell-to_warehouse="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.to_warehouse?.name }}</span>
                </template>
                <template #cell-quantity="{ row }">
                    {{ row.quantity }}
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" :label="row.status" />
                </template>

                <template #empty>
                    <EmptyState title="No transfers found" description="Create a transfer to move stock between warehouses.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" /></svg>
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
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';

defineProps({
    transfers: Object,
});

const columns = [
    { key: 'created_at', label: 'Date' },
    { key: 'product', label: 'Product' },
    { key: 'from_warehouse', label: 'From Warehouse' },
    { key: 'to_warehouse', label: 'To Warehouse' },
    { key: 'quantity', label: 'Quantity' },
    { key: 'status', label: 'Status' },
];
</script>
