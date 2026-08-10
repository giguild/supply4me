<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="Stock Adjustments">
                <template #actions>
                    <Link :href="route('stock.adjustments.create')" class="btn btn-accent btn-sm">New Adjustment</Link>
                </template>
            </PageHeader>

            <DataTable
                :columns="columns"
                :data="adjustments.data"
                :meta="adjustments"
                @page="(p) => router.get(route('stock.adjustments.index'), { page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-created_at="{ row }">
                    {{ row.created_at }}
                </template>
                <template #cell-product="{ row }">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.product?.name }}</span>
                </template>
                <template #cell-warehouse="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.warehouse?.name }}</span>
                </template>
                <template #cell-type="{ row }">
                    <StatusBadge
                        :value="row.type"
                        :label="row.type"
                        :variant="row.type === 'addition' ? 'success' : row.type === 'subtraction' ? 'danger' : 'info'"
                    />
                </template>
                <template #cell-quantity="{ row }">
                    <span :class="row.type === 'subtraction' ? 'text-red-600' : 'text-green-600'" class="font-medium">
                        {{ row.type === 'subtraction' ? '-' : '+' }}{{ row.quantity }}
                    </span>
                </template>
                <template #cell-reason="{ row }">
                    <span class="text-gray-500 dark:text-gray-400 max-w-xs truncate block">{{ row.reason }}</span>
                </template>
                <template #cell-user="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.user?.name }}</span>
                </template>

                <template #empty>
                    <EmptyState title="No adjustments found" description="Record a stock adjustment to update inventory levels.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                        </template>
                        <template #action>
                            <Link :href="route('stock.adjustments.create')" class="btn btn-accent">New Adjustment</Link>
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
    adjustments: Object,
});

const columns = [
    { key: 'created_at', label: 'Date' },
    { key: 'product', label: 'Product' },
    { key: 'warehouse', label: 'Warehouse' },
    { key: 'type', label: 'Type' },
    { key: 'quantity', label: 'Quantity' },
    { key: 'reason', label: 'Reason' },
    { key: 'user', label: 'Created By' },
];
</script>
