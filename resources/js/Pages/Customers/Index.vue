<template>
    <AppLayout :user="$page.props.auth.user">
            <PageHeader title="Customers">
                <template #actions>
                    <Link :href="route('customers.create')" class="btn btn-accent">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Customer
                    </Link>
                </template>
            </PageHeader>

            <!-- Filters -->
            <div class="card p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <SearchInput v-model="search" />
                    </div>
                    <div>
                        <select
                            v-model="filterStatus"
                            class="form-input"
                            @change="applyFilters"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <DataTable
                :columns="columns"
                :data="customers.data"
                :meta="customers"
                @page="goToPage"
                @rowClick="goToShow"
            >
                <template #cell-name="{ row }">
                    <div class="flex items-center gap-3">
                        <div v-if="row.avatar" class="w-8 h-8 rounded-full overflow-hidden shrink-0">
                            <img :src="`/storage/${row.avatar}`" :alt="row.name" class="w-full h-full object-cover" />
                        </div>
                        <div v-else class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300 shrink-0">
                            {{ row.name?.charAt(0) || '?' }}
                        </div>
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.name }}</span>
                    </div>
                </template>

                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status" />
                </template>

                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-2">
                        <Link :href="route('customers.show', row.id)" class="btn btn-outline btn-sm">View</Link>
                        <Link :href="route('customers.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                        <button @click="confirmDelete(row)" class="btn btn-danger btn-sm">Delete</button>
                    </div>
                </template>

                <template #empty>
                    <EmptyState title="No customers found" description="Get started by adding your first customer.">
                        <template #action>
                            <Link :href="route('customers.create')" class="btn btn-accent">Add Customer</Link>
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

const toast = useToast();

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const filterStatus = ref(props.filters?.status || '');

const columns = [
    { key: 'name', label: 'Name' },
    { key: 'email', label: 'Email' },
    { key: 'phone', label: 'Phone' },
    { key: 'status', label: 'Status' },
];

let searchTimeout = null;
const applyFilters = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('customers.index'), {
            search: search.value,
            status: filterStatus.value,
        }, { preserveState: true, replace: true });
    }, 300);
};

const goToPage = (page) => {
    router.get(route('customers.index'), {
        page,
        search: search.value,
        status: filterStatus.value,
    }, { preserveState: true, replace: true });
};

const goToShow = (row) => {
    router.visit(route('customers.show', row.id));
};

const confirmDelete = (customer) => {
    if (confirm(`Are you sure you want to delete "${customer.name}"?`)) {
        router.delete(route('customers.destroy', customer.id), {
            onSuccess: () => toast.success('Customer deleted successfully.'),
        });
    }
};
</script>
