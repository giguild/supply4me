<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Deliveries" subtitle="Track and manage all deliveries">
            <template #actions>
                <Link :href="route('deliveries.create')" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Delivery
                </Link>
            </template>
        </PageHeader>

        <div class="flex items-center gap-4 mb-6">
            <div class="flex-1 max-w-md">
                <SearchInput v-model="search" />
            </div>
            <select v-model="statusFilter" class="form-input w-auto" @change="onSearch">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="in_transit">In Transit</option>
                <option value="out_for_delivery">Out for Delivery</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <DataTable
            :columns="columns"
            :data="deliveries.data"
            :meta="deliveries.meta"
            @page="goToPage"
        >
            <template #cell-status="{ value }">
                <StatusBadge :value="value" :label="value?.replace('_', ' ')" />
            </template>

            <template #cell-delivery_number="{ row }">
                <Link :href="route('deliveries.show', row.id)" class="font-medium text-gray-900 dark:text-gray-100 hover:text-accent">
                    {{ row.delivery_number }}
                </Link>
            </template>

            <template #actions="{ row }">
                <Link :href="route('deliveries.show', row.id)" class="btn btn-outline btn-sm">
                    View
                </Link>
            </template>

            <template #empty>
                <EmptyState title="No deliveries found" description="Create your first delivery to get started.">
                    <template #action>
                        <Link :href="route('deliveries.create')" class="btn btn-primary">New Delivery</Link>
                    </template>
                </EmptyState>
            </template>
        </DataTable>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import SearchInput from '@/Components/UI/SearchInput.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';

const props = defineProps({
    deliveries: Object,
});

const search = ref('');
const statusFilter = ref('');

const columns = [
    { key: 'delivery_number', label: 'Delivery#' },
    { key: 'order_number', label: 'Order' },
    { key: 'driver_name', label: 'Driver' },
    { key: 'status', label: 'Status' },
    { key: 'scheduled_date', label: 'Date' },
];

let searchTimeout = null;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('deliveries.index'), {
            search: search.value,
            status: statusFilter.value,
        }, { preserveState: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('deliveries.index'), {
        page,
        search: search.value,
        status: statusFilter.value,
    }, { preserveState: true });
};
</script>
