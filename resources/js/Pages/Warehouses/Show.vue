<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="warehouse.name" subtitle="Warehouse details">
            <template #actions>
                <Link :href="route('warehouses.edit', warehouse.id)" class="btn btn-accent">Edit</Link>
            </template>
        </PageHeader>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Warehouse Details -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Warehouse Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Name</dt>
                        <dd class="text-sm font-medium">{{ warehouse.name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Code</dt>
                        <dd class="text-sm font-medium">{{ warehouse.code }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Branch</dt>
                        <dd class="text-sm font-medium">{{ warehouse.branch_name || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Type</dt>
                        <dd class="text-sm font-medium capitalize">{{ warehouse.type }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                        <dd><StatusBadge :value="warehouse.status" /></dd>
                    </div>
                </dl>
            </div>

            <!-- Location -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Location</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Address</dt>
                        <dd class="text-sm font-medium">{{ warehouse.address || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">City</dt>
                        <dd class="text-sm font-medium">{{ warehouse.city || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">State</dt>
                        <dd class="text-sm font-medium">{{ warehouse.state || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Country</dt>
                        <dd class="text-sm font-medium">{{ warehouse.country || '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Capacity -->
            <div class="card p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Capacity</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Maximum Capacity</dt>
                        <dd class="text-sm font-medium">{{ warehouse.capacity ? warehouse.capacity.toLocaleString() + ' units' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500 dark:text-gray-400">Created</dt>
                        <dd class="text-sm font-medium">{{ warehouse.created_at ? new Date(warehouse.created_at).toLocaleDateString() : '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Stock Items -->
        <div v-if="warehouse.stock_items && warehouse.stock_items.length" class="mb-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Stock Items</h3>
            <DataTable
                :columns="stockColumns"
                :data="warehouse.stock_items"
            >
                <template #cell-status="{ value }">
                    <StatusBadge :value="value" />
                </template>
            </DataTable>
        </div>

        <!-- Danger Zone -->
        <div class="card p-6 border border-red-200 dark:border-red-800">
            <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wider mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Deleting this warehouse will permanently remove all associated data.</p>
            <button @click="confirmDelete" class="btn btn-danger">Delete Warehouse</button>
        </div>
    </AppLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    warehouse: Object,
});

const toast = useToast();

const stockColumns = [
    { key: 'product_name', label: 'Product' },
    { key: 'qty_on_hand', label: 'Qty On Hand' },
    { key: 'qty_reserved', label: 'Qty Reserved' },
    { key: 'status', label: 'Status' },
];

const confirmDelete = () => {
    if (confirm(`Are you sure you want to delete "${props.warehouse.name}"? This action cannot be undone.`)) {
        router.delete(route('warehouses.destroy', props.warehouse.id), {
            onSuccess: () => {
                toast.success('Warehouse deleted successfully.');
                router.visit(route('warehouses.index'));
            },
        });
    }
};
</script>
