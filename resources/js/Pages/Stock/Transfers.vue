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
                :mobileColumns="mobileColumns"
                :data="transfers.data"
                :meta="transfers"
                @page="(p) => router.get(route('stock.transfers'), { page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-transfer_number="{ row }">
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.transfer_number }}</span>
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

                <template #actions="{ row }">
                    <div v-if="row.status === 'pending_approval' || row.status === 'approved' || row.status === 'in_transit'"
                         class="flex justify-end gap-2">
                        <button v-if="row.status === 'pending_approval'" type="button" class="btn btn-sm btn-outline" @click="approve(row)">
                            Approve
                        </button>
                        <button v-if="row.status === 'approved'" type="button" class="btn btn-sm btn-outline" @click="ship(row)">
                            Ship
                        </button>
                        <button v-if="row.status === 'in_transit'" type="button" class="btn btn-sm btn-accent" @click="receive(row)">
                            Receive
                        </button>
                    </div>
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
    { key: 'transfer_number', label: 'Number' },
    { key: 'product', label: 'Product' },
    { key: 'from_warehouse', label: 'From Warehouse' },
    { key: 'to_warehouse', label: 'To Warehouse' },
    { key: 'quantity', label: 'Quantity' },
    { key: 'status', label: 'Status' },
];

const mobileColumns = [
    { key: 'transfer_number', label: 'Number' },
    { key: 'product', label: 'Product' },
    { key: 'quantity', label: 'Qty' },
    { key: 'status', label: 'Status' },
];

const approve = (row) => {
    if (confirm(`Approve transfer ${row.transfer_number}?`)) {
        router.post(route('stock.transfers.approve', row.id), {}, { preserveState: true });
    }
};

const ship = (row) => {
    if (confirm(`Ship transfer ${row.transfer_number}?`)) {
        router.post(route('stock.transfers.ship', row.id), {}, { preserveState: true });
    }
};

const receive = (row) => {
    if (confirm(`Receive transfer ${row.transfer_number} and add stock to the destination warehouse?`)) {
        router.post(route('stock.transfers.receive', row.id), {}, { preserveState: true });
    }
};
</script>