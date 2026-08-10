<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Sales Report" subtitle="Analyze sales performance and trends" />

        <div class="card rounded-2xl p-4 mb-6">
            <form @submit.prevent="loadReport" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="form-label">Start Date</label>
                    <input v-model="filters.start_date" type="date" class="form-input" />
                </div>
                <div class="flex-1">
                    <label class="form-label">End Date</label>
                    <input v-model="filters.end_date" type="date" class="form-input" />
                </div>
                <button type="submit" class="btn btn-primary">
                    Generate Report
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <StatCard
                label="Total Revenue"
                :value="data.total_revenue ?? 0"
                prefix="₦ "
                subtitle="for selected period"
            />
            <StatCard
                label="Orders Count"
                :value="data.orders_count ?? 0"
                subtitle="total orders"
            />
            <StatCard
                label="Average Order Value"
                :value="data.average_order_value ?? 0"
                prefix="₦ "
                subtitle="per order"
            />
        </div>

        <div class="card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold">Top Products</h3>
            </div>
            <DataTable
                :columns="productColumns"
                :data="data.top_products ?? []"
            >
                <template #empty>
                    <div class="text-center py-8">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No product data</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Product sales data will appear here once orders are completed.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import DataTable from '@/Components/UI/DataTable.vue';

const props = defineProps({
    data: Object,
    filters: Object,
});

const filters = reactive({
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
});

const productColumns = [
    { key: 'name', label: 'Product' },
    { key: 'quantity_sold', label: 'Quantity Sold' },
    { key: 'total_revenue', label: 'Total Revenue' },
];

const loadReport = () => {
    router.get(route('reports.sales'), filters, { preserveState: true });
};
</script>
