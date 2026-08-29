<template>
  <AppLayout :user="$page.props.auth.user">
      <PageHeader
        title="Dashboard"
        :subtitle="`Welcome back, ${user?.name}`"
      />

      <!-- Stats Grid -->
      <div v-if="hasStats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        <StatCard
          v-if="stats.can_orders"
          label="Total Orders"
          :value="stats.total_orders ?? 0"
          :subtitle="`${stats.pending_orders ?? 0} pending`"
        />
        <StatCard
          v-if="stats.can_customers"
          label="Total Customers"
          :value="stats.total_customers ?? 0"
        />
        <StatCard
          v-if="stats.can_products"
          label="Total Products"
          :value="stats.total_products ?? 0"
          :subtitle="`${stats.low_stock_count ?? 0} low stock`"
        />
        <StatCard
          v-if="stats.can_payments"
          label="Pending Payments"
          :value="stats.pending_payments ?? 0"
          subtitle="awaiting approval"
        />
        <StatCard
          v-if="stats.can_orders"
          label="Monthly Revenue"
          :value="stats.monthly_revenue ?? 0"
          prefix="₦"
          subtitle="this month"
        />
        <StatCard
          v-if="stats.can_deliveries"
          label="My Deliveries"
          :value="stats.my_deliveries ?? 0"
          :subtitle="`${stats.active_deliveries ?? 0} active`"
        />
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lists -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Recent Orders -->
          <div v-if="showRecentOrders" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Orders</h3>
              <Link
                :href="route('orders.index')"
                class="text-sm font-medium text-blue-600 hover:text-blue-700"
              >
                View All
              </Link>
            </div>
            <div class="overflow-x-auto">
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
                    <td class="py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(order.total_amount) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Recent Payments -->
          <div v-if="showRecentPayments" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Payments</h3>
              <Link
                :href="route('payments.index')"
                class="text-sm font-medium text-blue-600 hover:text-blue-700"
              >
                View All
              </Link>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-gray-100 dark:border-gray-700">
                    <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Reference</th>
                    <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Customer</th>
                    <th class="text-left py-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                    <th class="text-right py-3 font-medium text-gray-500 dark:text-gray-400">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="payment in stats.recent_payments"
                    :key="payment.id"
                    class="border-b border-gray-50 last:border-0"
                  >
                    <td class="py-3 font-medium text-gray-900 dark:text-gray-100 font-mono text-xs">{{ payment.reference }}</td>
                    <td class="py-3 text-gray-600 dark:text-gray-400">{{ payment.customer?.name }}</td>
                    <td class="py-3">
                      <StatusBadge :value="payment.status" />
                    </td>
                    <td class="py-3 text-right font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(payment.amount) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Empty state -->
          <div v-if="!hasLists" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Welcome back, {{ user?.name }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              There's no recent activity to show here. Use the navigation or quick actions to get started.
            </p>
          </div>
        </div>

        <!-- Quick Actions -->
        <div v-if="quickActions.length" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
          <div class="space-y-3">
            <Link
              v-for="action in quickActions"
              :key="action.key"
              :href="route(action.route)"
              class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
              <div :class="iconFor(action.key).box">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconFor(action.key).icon" />
                </svg>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ action.label }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ action.description }}</p>
              </div>
            </Link>
          </div>
        </div>
      </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import PageHeader from '@/Components/UI/PageHeader.vue'
import StatCard from '@/Components/UI/StatCard.vue'
import StatusBadge from '@/Components/UI/StatusBadge.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  user: Object,
  stats: Object,
})

const quickActions = computed(() => props.stats.quick_actions ?? [])
const hasStats = computed(() =>
  props.stats.can_orders ||
  props.stats.can_customers ||
  props.stats.can_products ||
  props.stats.can_payments ||
  props.stats.can_deliveries
)
const showRecentOrders = computed(() =>
  props.stats.can_orders && (props.stats.recent_orders?.length > 0)
)
const showRecentPayments = computed(() =>
  props.stats.can_payments && (props.stats.recent_payments?.length > 0)
)
const hasLists = computed(() => showRecentOrders.value || showRecentPayments.value)

const ICONS = {
  order: { box: 'p-2 bg-blue-100 rounded-lg', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
  customer: { box: 'p-2 bg-green-100 rounded-lg', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' },
  product: { box: 'p-2 bg-purple-100 rounded-lg', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
  invoice: { box: 'p-2 bg-yellow-100 rounded-lg', icon: 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z' },
  payment: { box: 'p-2 bg-teal-100 rounded-lg', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2h-8a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z' },
  grn: { box: 'p-2 bg-orange-100 rounded-lg', icon: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4' },
  picklist: { box: 'p-2 bg-indigo-100 rounded-lg', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' },
  packinglist: { box: 'p-2 bg-cyan-100 rounded-lg', icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z' },
  shipment: { box: 'p-2 bg-pink-100 rounded-lg', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
  delivery: { box: 'p-2 bg-gray-100 rounded-lg dark:bg-gray-700', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
}

const DEFAULT_ICON = { box: 'p-2 bg-gray-100 rounded-lg dark:bg-gray-700', icon: 'M12 9v3m0 0v3m0-3h3m-3 0H9m3-6a9 9 0 110 18 9 9 0 010-18z' }

const iconFor = (key) => ICONS[key] || DEFAULT_ICON

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN',
    minimumFractionDigits: 2,
  }).format(value ?? 0)
}
</script>