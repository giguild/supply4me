<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="Packing Lists">
                <template #actions>
                    <Link :href="route('packing-lists.create')" class="btn btn-accent btn-sm">New Packing List</Link>
                </template>
            </PageHeader>

            <DataTable
                :columns="columns"
                :data="packingLists.data"
                :meta="packingLists"
                @rowClick="(row) => router.get(route('packing-lists.show', row.id))"
                @page="(p) => router.get(route('packing-lists.index'), { page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-packing_list_number="{ row }">
                    <span class="font-medium text-accent">{{ row.packing_list_number }}</span>
                </template>
                <template #cell-order="{ row }">
                    <span class="text-gray-900 dark:text-gray-100">{{ row.order?.order_number }}</span>
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" :label="row.status" />
                </template>
                <template #cell-created_at="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.created_at }}</span>
                </template>

                <template #empty>
                    <EmptyState title="No packing lists found" description="Create a packing list to start packing orders.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </template>
                        <template #action>
                            <Link :href="route('packing-lists.create')" class="btn btn-accent">New Packing List</Link>
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
    packingLists: Object,
});

const columns = [
    { key: 'packing_list_number', label: 'PL#' },
    { key: 'order', label: 'Order' },
    { key: 'status', label: 'Status' },
    { key: 'created_at', label: 'Created' },
];
</script>
