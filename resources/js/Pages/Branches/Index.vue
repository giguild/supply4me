<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Branches" subtitle="Manage company branches and locations">
            <template #actions>
                <Link :href="route('branches.create')" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Branch
                </Link>
            </template>
        </PageHeader>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
            <div class="flex-1 max-w-md">
                <SearchInput v-model="search" />
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="branches.data"
            :meta="branches.meta"
            @page="goToPage"
        >
            <template #cell-status="{ value }">
                <StatusBadge :value="value" />
            </template>

            <template #actions="{ row }">
                <div class="flex items-center gap-2">
                    <Link :href="route('branches.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                    <button class="btn btn-danger btn-sm" @click="confirmDelete(row)">Delete</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No branches found" description="Add your first branch to get started.">
                    <template #action>
                        <Link :href="route('branches.create')" class="btn btn-primary">New Branch</Link>
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
    branches: Object,
});

const toast = useToast();
const search = ref('');

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'city', label: 'City' },
    { key: 'country', label: 'Country' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const onSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('branches.index'), { search: search.value }, { preserveState: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('branches.index'), { page, search: search.value }, { preserveState: true });
};

const confirmDelete = (branch) => {
    if (confirm(`Are you sure you want to delete "${branch.name}"?`)) {
        router.delete(route('branches.destroy', branch.id), {
            onSuccess: () => {
                toast.success('Branch deleted successfully.');
            },
        });
    }
};
</script>
