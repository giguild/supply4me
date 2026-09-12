<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Shipping Carriers" subtitle="Manage carrier accounts used by shipments">
            <template #actions>
                <Link :href="route('shipping-carriers.create')" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Carrier
                </Link>
            </template>
        </PageHeader>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
            <div class="flex-1 max-w-md">
                <SearchInput v-model="search" @update:modelValue="onSearch" />
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="carriers.data"
            :meta="carriers.meta"
            @page="goToPage"
        >
            <template #cell-status="{ value }">
                <StatusBadge :value="value" />
            </template>

            <template #cell-tracking="{ row }">
                <span v-if="row.tracking_url" class="text-xs text-blue-600 dark:text-blue-400 line-clamp-1">{{ row.tracking_url }}</span>
                <span v-else class="text-gray-400">—</span>
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link :href="route('shipping-carriers.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                    <button class="btn btn-danger btn-sm" @click="confirmDelete(row)">Delete</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No carriers found" description="Add your first shipping carrier to get started.">
                    <template #action>
                        <Link :href="route('shipping-carriers.create')" class="btn btn-primary">New Carrier</Link>
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
    carriers: Object,
});

const toast = useToast();
const search = ref('');

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'code', label: 'Code' },
    { key: 'tracking', label: 'Tracking URL' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('shipping-carriers.index'), { search: search.value }, { preserveState: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('shipping-carriers.index'), { page, search: search.value }, { preserveState: true });
};

const confirmDelete = (carrier) => {
    if (confirm(`Are you sure you want to delete "${carrier.name}"?`)) {
        router.delete(route('shipping-carriers.destroy', carrier.id), {
            onSuccess: () => {
                toast.success('Carrier deleted successfully.');
            },
        });
    }
};
</script>