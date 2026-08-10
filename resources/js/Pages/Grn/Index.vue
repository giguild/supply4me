<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="Goods Received Notes">
                <template #actions>
                    <Link :href="route('grn.create')" class="btn btn-accent btn-sm">New GRN</Link>
                </template>
            </PageHeader>

            <DataTable
                :columns="columns"
                :data="grns.data"
                :meta="grns"
                @rowClick="(row) => router.get(route('grn.show', row.id))"
                @page="(p) => router.get(route('grn.index'), { page: p }, { preserveState: true, replace: true })"
            >
                <template #cell-grn_number="{ row }">
                    <span class="font-medium text-accent">{{ row.grn_number }}</span>
                </template>
                <template #cell-supplier="{ row }">
                    <span class="text-gray-900 dark:text-gray-100">{{ row.supplier?.name }}</span>
                </template>
                <template #cell-created_at="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.created_at }}</span>
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" :label="row.status" />
                </template>
                <template #cell-items_count="{ row }">
                    <span class="text-gray-500 dark:text-gray-400">{{ row.items_count ?? 0 }}</span>
                </template>

                <template #empty>
                    <EmptyState title="No goods received notes" description="Create a GRN to record received goods from suppliers.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </template>
                        <template #action>
                            <Link :href="route('grn.create')" class="btn btn-accent">New GRN</Link>
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
    grns: Object,
});

const columns = [
    { key: 'grn_number', label: 'GRN#' },
    { key: 'supplier', label: 'Supplier' },
    { key: 'created_at', label: 'Date' },
    { key: 'status', label: 'Status' },
    { key: 'items_count', label: 'Items Count' },
];
</script>
