<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Financial Report" subtitle="Revenue, expenses, and profit analysis" />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <StatCard
                label="Revenue"
                :value="data.revenue ?? 0"
                prefix="₦ "
                subtitle="total income"
            />
            <StatCard
                label="Expenses"
                :value="data.expenses ?? 0"
                prefix="₦ "
                subtitle="total costs"
            />
            <StatCard
                label="Profit"
                :value="data.profit ?? 0"
                prefix="₦ "
                subtitle="net income"
            />
        </div>

        <div class="card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold">Monthly Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Revenue</th>
                            <th>Expenses</th>
                            <th>Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="month in data.monthly_breakdown ?? []" :key="month.name">
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ month.name }}</td>
                            <td class="text-green-600 font-medium">₦ {{ formatCurrency(month.revenue) }}</td>
                            <td class="text-red-600 font-medium">₦ {{ formatCurrency(month.expenses) }}</td>
                            <td class="font-medium" :class="month.profit >= 0 ? 'text-blue-600' : 'text-red-600'">
                                ₦ {{ formatCurrency(month.profit) }}
                            </td>
                        </tr>
                        <tr v-if="!data.monthly_breakdown?.length">
                            <td colspan="4">
                                <div class="text-center py-8">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No financial data</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Financial data will appear here once you have orders and invoices.</p>
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

const props = defineProps({
    data: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value);
};
</script>
