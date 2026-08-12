<template>
  <AppLayout :user="$page.props.auth.user">
      <PageHeader
        title="Dashboard"
        :subtitle="`Welcome back, ${user?.name}`"
      />

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <StatCard
          label="Total Orders"
          :value="stats.total_orders ?? 0"
          :subtitle="`${stats.pending_orders ?? 0} pending`"
        />
        <StatCard
          label="Total Customers"
          :value="stats.total_customers ?? 0"
        />
        <StatCard
          label="Total Products"
          :value="stats.total_products ?? 0"
          :subtitle="`${stats.low_stock_count ?? 0} low stock`"
        />
        <StatCard
          label="Pending Payments"
          :value="stats.pending_payments ?? 0"
          :subtitle="`${stats.pending_payments ?? 0} awaiting approval`"
        />
        <StatCard
          label="Monthly Revenue"
          :value="formatCurrency(stats.monthly_revenue ?? 0)"
          :format="false"
        />
        <StatCard
          label="Pending Orders"
          :value="stats.pending_orders ?? 0"
          :subtitle="`${stats.pending_orders ?? 0} awaiting processing`"
        />
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Orders</h3>
            <Link
              :href="route('orders.index')"
              class="text-sm font-medium text-blue-600 hover:text-blue-700"
            >
              View All
            </Link>
          </div>
          <div v-if="stats.recent_orders?.length" class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-gray-100 dark:border-gray-700">
                  <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Order #</th>
                  <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Customer</th>
                  <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                  <th class="text-right py-3 font-medium text-gray-500 dark:text-gray-400">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="order in stats.recent_orders"
                  :key="order.id"
                  class="border-b border-gray-50 last:border-0"
                >
                  <td class="py-3 font-medium text-gray-900 dark:text-gray-100">#{{ order.order_number }}</td>
                  <td class="py-3 text-gray-600 dark:text-gray-400">{{ order.customer?.name }}</td>
                  <td class="py-3">
                    <StatusBadge :value="order.status" />
                  </td>
                   <td class="py-3 text-right font-medium text-gray-900 dark:text-gray-100">
                    ₦{{ formatCurrency(order.total_amount) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-else class="py-8 text-center text-gray-400 dark:text-gray-500">
            No recent orders
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
          <div class="space-y-3">
            <Link
              :href="route('orders.create')"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">New Order</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Create a purchase order</p>
              </div>
            </Link>

            <Link
              :href="route('customers.create')"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Add Customer</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Register a new customer</p>
              </div>
            </Link>

            <Link
              :href="route('products.create')"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Add Product</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Add to your inventory</p>
              </div>
            </Link>

            <Link
              :href="route('invoices.create')"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Create Invoice</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Bill your customers</p>
              </div>
            </Link>
          </div>
        </div>
      </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import PageHeader from '@/Components/UI/PageHeader.vue'
import StatCard from '@/Components/UI/StatCard.vue'
import StatusBadge from '@/Components/UI/StatusBadge.vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

defineProps({
  user: Object,
  stats: Object,
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-NG', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value)
}
</script>
