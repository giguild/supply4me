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
                :mobileColumns="mobileColumns"
                :data="adjustments.data"
                :meta="adjustments"
                @page="(p) => router.get(route('stock.adjustments'), { page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-adjustment_number="{ row }">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.adjustment_number }}</span>
                </template>
                <template #cell-product="{ row }">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.product?.name }}</span>
                </template>
                <template #cell-warehouse="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.warehouse?.name }}</span>
                </template>
                <template #cell-type="{ row }">
                    <StatusBadge :value="row.type" :label="row.type" />
                </template>
                <template #cell-quantity="{ row }">
                    <span :class="row.quantity >= 0 ? 'text-green-600' : 'text-red-600'" class="font-medium">
                        {{ row.quantity >= 0 ? '+' : '' }}{{ row.quantity }}
                    </span>
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" :label="row.status" />
                </template>
                <template #cell-user="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.user?.name }}</span>
                </template>

                <template #actions="{ row }">
                    <div v-if="row.status === 'pending'" class="flex justify-end gap-2">
                        <button type="button" class="btn btn-sm btn-outline" @click="approve(row)">
                            Approve
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" @click="reject(row)">
                            Reject
                        </button>
                    </div>
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
    { key: 'adjustment_number', label: 'Number' },
    { key: 'product', label: 'Product' },
    { key: 'warehouse', label: 'Warehouse' },
    { key: 'type', label: 'Type' },
    { key: 'quantity', label: 'Net Change' },
    { key: 'status', label: 'Status' },
    { key: 'user', label: 'Created By' },
];

const mobileColumns = [
    { key: 'adjustment_number', label: 'Number' },
    { key: 'product', label: 'Product' },
    { key: 'quantity', label: 'Net Change' },
    { key: 'status', label: 'Status' },
];

const approve = (row) => {
    if (confirm(`Approve adjustment ${row.adjustment_number}?`)) {
        router.post(route('stock.adjustments.approve', row.id), {}, { preserveState: true });
    }
};

const reject = (row) => {
    if (confirm(`Reject adjustment ${row.adjustment_number}? Stock changes will be restored.`)) {
        router.post(route('stock.adjustments.reject', row.id), {}, { preserveState: true });
    }
};
</script>