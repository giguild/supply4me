<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="Pick Lists">
                <template #actions>
                    <Link :href="route('pick-lists.create')" class="btn btn-accent btn-sm">New Pick List</Link>
                </template>
            </PageHeader>

            <DataTable
                :columns="columns"
                :data="pickLists.data"
                :meta="pickLists"
                @rowClick="(row) => router.get(route('pick-lists.show', row.id))"
                @page="(p) => router.get(route('pick-lists.index'), { page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-pick_list_number="{ row }">
                    <span class="font-medium text-accent">{{ row.pick_list_number }}</span>
                </template>
                <template #cell-order="{ row }">
                    <span class="text-gray-900 dark:text-gray-100">{{ row.order?.order_number }}</span>
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" :label="row.status" />
                </template>
                <template #cell-assigned_to="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.assigned_to?.name ?? 'Unassigned' }}</span>
                </template>
                <template #cell-created_at="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.created_at }}</span>
                </template>

                <template #empty>
                    <EmptyState title="No pick lists found" description="Create a pick list to start fulfilling orders.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </template>
                        <template #action>
                            <Link :href="route('pick-lists.create')" class="btn btn-accent">New Pick List</Link>
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
    pickLists: Object,
});

const columns = [
    { key: 'pick_list_number', label: 'PL#' },
    { key: 'order', label: 'Order' },
    { key: 'status', label: 'Status' },
    { key: 'assigned_to', label: 'Assigned To' },
    { key: 'created_at', label: 'Created' },
];
</script>
