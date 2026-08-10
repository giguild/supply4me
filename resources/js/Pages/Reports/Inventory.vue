<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Inventory Report" subtitle="Monitor stock levels and inventory value" />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <StatCard
                label="Total Products"
                :value="data.total_products ?? 0"
                subtitle="in inventory"
            />
            <StatCard
                label="Low Stock Items"
                :value="data.low_stock_count ?? 0"
                subtitle="need restocking"
            />
            <StatCard
                label="Total Stock Value"
                :value="data.total_value ?? 0"
                prefix="₦ "
                subtitle="inventory value"
            />
        </div>

        <div class="card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold">Stock Levels</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Min Level</th>
                            <th>Status</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in data.stock_levels ?? []" :key="item.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ item.name }}</td>
                            <td class="font-mono text-gray-600 dark:text-gray-400">{{ item.sku }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.min_stock_level }}</td>
                            <td>
                                <StatusBadge :value="stockStatusLabel(item)" :variant="item.quantity <= 0 ? 'danger' : item.quantity <= item.min_stock_level ? 'warning' : 'success'" />
                            </td>
                            <td>₦ {{ formatCurrency(item.value) }}</td>
                        </tr>
                        <tr v-if="!data.stock_levels?.length">
                            <td colspan="6">
                                <div class="text-center py-8">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No inventory data</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Inventory data will appear here once products are added to warehouses.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

const props = defineProps({
    data: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};

const stockStatusLabel = (item) => {
    if (item.quantity <= 0) return 'Out of Stock';
    if (item.quantity <= item.min_stock_level) return 'Low Stock';
    return 'In Stock';
};
</script>
