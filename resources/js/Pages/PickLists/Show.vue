<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader :title="`Pick List ${pickList.pick_list_number}`">
                <template #actions>
                    <Link :href="route('pick-lists.index')" class="btn btn-outline btn-sm">Back</Link>
                </template>
            </PageHeader>

            <div class="card p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Order</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ pickList.order?.order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</p>
                        <StatusBadge :value="pickList.status" :label="pickList.status" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Assigned To</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ pickList.assigned_to?.name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Created</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ pickList.created_at }}</p>
                    </div>
                </div>
                <div v-if="pickList.notes" class="mt-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                    <p class="text-sm text-gray-900">{{ pickList.notes }}</p>
                </div>
            </div>

            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold">Items</h3>
                </div>
                <DataTable
                    :columns="itemColumns"
                    :data="pickList.items || []"
                >
                    <template #cell-product="{ row }">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.product?.name }}</span>
                    </template>
                    <template #cell-bin_location="{ row }">
                        <span class="text-gray-500 dark:text-gray-400">{{ row.bin_location ?? '-' }}</span>
                    </template>
                    <template #cell-status="{ row }">
                        <StatusBadge
                            :value="row.status ?? 'pending'"
                            :label="row.status ?? 'pending'"
                        />
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
    pickList: Object,
});

const itemColumns = [
    { key: 'product', label: 'Product' },
    { key: 'quantity', label: 'Quantity' },
    { key: 'bin_location', label: 'Bin Location' },
    { key: 'status', label: 'Status' },
];
</script>
