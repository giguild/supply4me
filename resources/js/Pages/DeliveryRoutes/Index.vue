<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Delivery Routes" subtitle="Manage delivery routes and stops">
            <template #actions>
                <Link :href="route('delivery-routes.create')" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Route
                </Link>
            </template>
        </PageHeader>

        <div class="flex items-center gap-4 mb-6">
            <div class="flex-1 max-w-md">
                <SearchInput v-model="search" />
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="deliveryRoutes.data"
            :meta="deliveryRoutes.meta"
            @page="goToPage"
        >
            <template #cell-status="{ value }">
                <StatusBadge :value="value" />
            </template>

            <template #cell-stops_count="{ row }">
                <span class="text-gray-600 dark:text-gray-400">{{ row.stops_count ?? row.stops?.length ?? 0 }} stops</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link :href="route('delivery-routes.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                    <button class="btn btn-danger btn-sm" @click="confirmDelete(row)">Delete</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No delivery routes found" description="Create your first delivery route to get started.">
                    <template #action>
                        <Link :href="route('delivery-routes.create')" class="btn btn-primary">New Route</Link>
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
import { useToast } from '@/composables/useToast';

const props = defineProps({
    deliveryRoutes: Object,
});

const toast = useToast();
const search = ref('');

const columns = [
    { key: 'name', label: 'Route Name' },
    { key: 'description', label: 'Description' },
    { key: 'stops_count', label: 'Stops' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('delivery-routes.index'), { search: search.value }, { preserveState: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('delivery-routes.index'), { page, search: search.value }, { preserveState: true });
};

const confirmDelete = (deliveryRoute) => {
    if (confirm(`Are you sure you want to delete "${deliveryRoute.name}"?`)) {
        router.delete(route('delivery-routes.destroy', deliveryRoute.id), {
            onSuccess: () => {
                toast.success('Delivery route deleted successfully.');
            },
        });
    }
};
</script>
