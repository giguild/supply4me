<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="`Sales Rep: ${rep.name}`" subtitle="Performance overview and assigned customers">
            <template #actions>
                <Link :href="route('sales-reps.index')" class="btn btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </Link>
            </template>
        </PageHeader>

        <!-- Rep Info -->
        <div class="card rounded-2xl p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-accent/10 flex items-center justify-center text-accent text-2xl font-bold">
                    {{ rep.name.charAt(0) }}
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ rep.name }}</h3>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span>{{ rep.email }}</span>
                        <span v-if="rep.phone">{{ rep.phone }}</span>
                        <span v-if="rep.region">{{ rep.region }}</span>
                        <span v-if="rep.state">{{ rep.state }}</span>
                    </div>
                </div>
                <StatusBadge :value="rep.status" />
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatCard label="Total Customers" :value="stats.total_customers" :subtitle="`${stats.active_customers} active`" />
            <StatCard label="Total Orders" :value="stats.total_orders" :subtitle="`₦${formatCurrency(stats.total_revenue)} revenue`" />
            <StatCard label="Avg Order Value" :value="`₦${formatCurrency(stats.avg_order_value)}`" :format="false" />
            <StatCard label="Payments Collected" :value="`₦${formatCurrency(stats.collected_amount)}`" :format="false" :subtitle="`${stats.pending_payments} pending`" />
        </div>

        <!-- Recent Orders -->
        <div class="card rounded-2xl overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in recentOrders" :key="order.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ order.order_number || order.id.slice(0, 8) }}</td>
                            <td class="text-sm text-gray-600 dark:text-gray-400">{{ order.customer?.name || '-' }}</td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">{{ formatDate(order.order_date) }}</td>
                            <td><StatusBadge :value="order.status" /></td>
                            <td class="text-right font-medium text-gray-900 dark:text-gray-100">₦{{ formatCurrency(order.total_amount) }}</td>
                        </tr>
                        <tr v-if="!recentOrders.length">
                            <td colspan="5">
                                <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400">No orders yet</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assigned Customers -->
        <div class="card rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Assigned Customers</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Orders</th>
                            <th class="text-right">Total Spent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="customer in customers.data" :key="customer.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ customer.name }}</td>
                            <td class="text-sm text-gray-600 dark:text-gray-400">{{ customer.email || '-' }}</td>
                            <td class="text-sm text-gray-500 dark:text-gray-400">{{ customer.phone || '-' }}</td>
                            <td class="text-sm text-gray-600 dark:text-gray-400">{{ customer.orders_count ?? 0 }}</td>
                            <td class="text-right font-medium text-gray-900 dark:text-gray-100">₦{{ formatCurrency(customer.orders_sum_total_amount ?? 0) }}</td>
                            <td><StatusBadge :value="customer.status" /></td>
                        </tr>
                        <tr v-if="!customers.data.length">
                            <td colspan="6">
                                <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400">No customers assigned</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div v-if="customers.last_page > 1" class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>Showing {{ customers.data.length }} of {{ customers.total }} customers</span>
                    <div class="flex gap-2">
                        <button v-if="customers.current_page > 1" @click="goToPage(customers.current_page - 1)" class="btn btn-outline btn-sm">Previous</button>
                        <button v-if="customers.current_page < customers.last_page" @click="goToPage(customers.current_page + 1)" class="btn btn-outline btn-sm">Next</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    rep: Object,
    stats: Object,
    recentOrders: Array,
    customers: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value || 0);
};

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-NG', { year: 'numeric', month: 'short', day: 'numeric' });
};

const goToPage = (page) => {
    router.get(route('sales-reps.show', props.rep.id), { page }, { preserveState: true });
};
</script>
