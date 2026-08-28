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
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">GRN Number</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ grn.grn_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Supplier</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.supplier?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Warehouse</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.warehouse?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Received Date</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.receiving_date }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</p>
                        <StatusBadge :value="grn.status" :label="grn.status?.replace('_', ' ')" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Received By</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ grn.receivedBy?.name ?? 'N/A' }}</p>
                    </div>
                </div>
                <div v-if="grn.notes" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
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
                    <template #cell-condition="{ row }">
                        <span class="text-gray-500 dark:text-gray-400">{{ row.condition || '-' }}</span>
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
    { key: 'expected_quantity', label: 'Qty Ordered' },
    { key: 'received_quantity', label: 'Qty Received' },
    { key: 'accepted_quantity', label: 'Qty Accepted' },
    { key: 'rejected_quantity', label: 'Qty Rejected' },
    { key: 'condition', label: 'Condition' },
];
</script>
