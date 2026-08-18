<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Sales Rep Dashboard" :subtitle="`Welcome back, ${$page.props.auth.user?.name}`" />

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard
                label="My Customers"
                :value="stats.total_customers ?? 0"
                subtitle="assigned to you"
            />
            <StatCard
                label="Active Customers"
                :value="stats.active_customers ?? 0"
                subtitle="currently active"
            />
            <StatCard
                label="Total Orders"
                :value="stats.total_orders ?? 0"
                subtitle="from your customers"
            />
            <StatCard
                label="Total Revenue"
                :value="formatCurrency(stats.total_revenue ?? 0)"
                :format="false"
                prefix="₦"
                subtitle="from your customers"
            />
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Customers -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Customers</h3>
                    <Link
                        :href="route('sales-rep.customers')"
                        class="text-sm font-medium text-blue-600 hover:text-blue-700"
                    >
                        View All
                    </Link>
                </div>
                <div v-if="recentCustomers?.length" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Customer</th>
                                <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Number</th>
                                <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="customer in recentCustomers"
                                :key="customer.id"
                                class="border-b border-gray-50 last:border-0"
                            >
                                <td class="py-3 font-medium text-gray-900 dark:text-gray-100">{{ customer.name }}</td>
                                <td class="py-3 text-gray-600 dark:text-gray-400 font-mono text-xs">{{ customer.customer_number }}</td>
                                <td class="py-3">
                                    <StatusBadge :value="customer.status" />
                                </td>
                                <td class="py-3 text-gray-600 dark:text-gray-400">{{ customer.city || customer.state || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="py-8 text-center text-gray-400 dark:text-gray-500">
                    No customers assigned yet
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Customers by Status</h3>
                    <div class="space-y-3">
                        <div v-for="(count, status) in customersByStatus" :key="status" class="flex items-center justify-between">
                            <StatusBadge :value="status" />
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ count }}</span>
                        </div>
                        <div v-if="!Object.keys(customersByStatus || {}).length" class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                            No data yet
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recent Orders</h3>
                    <div v-if="recentOrders?.length" class="space-y-3">
                        <div v-for="order in recentOrders" :key="order.id" class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-gray-700 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ order.order_number }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ order.customer?.name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">₦{{ formatCurrency(order.total_amount) }}</p>
                                <StatusBadge :value="order.status" />
                            </div>
                        </div>
                    </div>
                    <div v-else class="py-4 text-center text-sm text-gray-400 dark:text-gray-500">
                        No orders yet
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

defineProps({
    stats: Object,
    recentCustomers: Array,
    recentOrders: Array,
    customersByStatus: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
};
</script>
