<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader :title="`GRN ${grn.grn_number}`">
                <template #actions>
                    <Link :href="route('grn.index')" class="btn btn-outline btn-sm">Back</Link>
                </template>
            </PageHeader>

            <div class="card p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Supplier</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.supplier?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Warehouse</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.warehouse?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Expected Date</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.expected_date }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</p>
                        <StatusBadge :value="grn.status" :label="grn.status" />
                    </div>
                </div>
                <div v-if="grn.notes" class="mt-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                    <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.notes }}</p>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold">Items</h3>
                </div>
                <DataTable
                    :columns="itemColumns"
                    :data="grn.items || []"
                >
                    <template #cell-product="{ row }">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.product?.name }}</span>
                    </template>
                    <template #cell-unit_cost="{ row }">
                        {{ Number(row.unit_cost).toFixed(2) }}
                    </template>
                    <template #cell-total="{ row }">
                        {{ (row.received_qty * row.unit_cost).toFixed(2) }}
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

defineProps({
    grn: Object,
});

const itemColumns = [
    { key: 'product', label: 'Product' },
    { key: 'expected_qty', label: 'Expected Qty' },
    { key: 'received_qty', label: 'Received Qty' },
    { key: 'unit_cost', label: 'Unit Cost' },
    { key: 'total', label: 'Total' },
];
</script>
