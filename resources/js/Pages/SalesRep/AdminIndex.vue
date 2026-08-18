<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Sales Reps" subtitle="Monitor sales rep performance and KPIs" />

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard
                label="Total Sales Reps"
                :value="salesReps.length"
                subtitle="active reps"
            />
            <StatCard
                label="Total Customers"
                :value="totals.total_customers"
                subtitle="assigned to reps"
            />
            <StatCard
                label="Total Revenue"
                :value="formatCurrency(totals.total_revenue)"
                :format="false"
                prefix="₦"
                subtitle="from rep customers"
            />
            <StatCard
                label="Total Collected"
                :value="formatCurrency(totals.total_collected)"
                :format="false"
                prefix="₦"
                subtitle="approved + completed"
            />
        </div>

        <!-- Filters -->
        <div class="card rounded-2xl p-4 mb-6">
            <form @submit.prevent="applyFilters" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input v-model="filters.search" type="text" placeholder="Search sales reps..." class="form-input" />
                </div>
                <div class="w-full sm:w-48">
                    <select v-model="filters.status" class="form-input">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <!-- Sales Reps Table -->
        <div class="card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Sales Rep</th>
                            <th>Region</th>
                            <th>Customers</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>Avg Order</th>
                            <th>Collected</th>
                            <th>Payment Rate</th>
                            <th>Pending</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rep in filteredReps" :key="rep.id">
                            <td>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ rep.name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ rep.email }}</p>
                                </div>
                            </td>
                            <td>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ rep.region || rep.state || '-' }}</span>
                                <span v-if="rep.state" class="block text-xs text-gray-400 dark:text-gray-500">{{ rep.state }}</span>
                            </td>
                            <td>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ rep.total_customers }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ rep.active_customers }} active</p>
                            </td>
                            <td class="text-sm text-gray-600 dark:text-gray-400">{{ rep.total_orders }}</td>
                            <td class="font-medium text-gray-900 dark:text-gray-100">₦{{ formatCurrency(rep.total_revenue) }}</td>
                            <td class="text-sm text-gray-600 dark:text-gray-400">₦{{ formatCurrency(rep.avg_order_value) }}</td>
                            <td class="font-medium text-green-600 dark:text-green-400">₦{{ formatCurrency(rep.collected_amount) }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full" :style="{ width: Math.min(rep.payment_completion_rate, 100) + '%' }"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ rep.payment_completion_rate }}%</span>
                                </div>
                            </td>
                            <td>
                                <span v-if="rep.pending_payments > 0" class="text-sm font-medium text-yellow-600 dark:text-yellow-400">{{ rep.pending_payments }}</span>
                                <span v-else class="text-sm text-gray-400 dark:text-gray-500">0</span>
                            </td>
                            <td>
                                <StatusBadge :value="rep.status" />
                            </td>
                        </tr>
                        <tr v-if="!filteredReps.length">
                            <td colspan="10">
                                <div class="text-center py-8">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No sales reps found</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Create a user with the "Sales Rep" role to see them here.</p>
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
import { computed, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

const props = defineProps({
    salesReps: Array,
    filters: Object,
});

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const totals = computed(() => {
    return props.salesReps.reduce((acc, rep) => {
        acc.total_customers += rep.total_customers;
        acc.total_revenue += rep.total_revenue;
        acc.total_collected += rep.collected_amount;
        return acc;
    }, { total_customers: 0, total_revenue: 0, total_collected: 0 });
});

const filteredReps = computed(() => {
    return props.salesReps.filter(rep => {
        const matchesSearch = !filters.search ||
            rep.name.toLowerCase().includes(filters.search.toLowerCase()) ||
            (rep.email || '').toLowerCase().includes(filters.search.toLowerCase());
        const matchesStatus = !filters.status || rep.status === filters.status;
        return matchesSearch && matchesStatus;
    });
});

const applyFilters = () => {
    router.get(route('sales-reps.index'), filters, { preserveState: true });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};
</script>
