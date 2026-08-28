<template>
  <AccountLayout :cartCount="cartCount" :customer="customer">
    <h1 class="text-2xl font-bold text-[var(--color-text)] mb-6">My Orders</h1>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
      <input v-model="search" type="text" placeholder="Search by order number..."
        class="flex-1 px-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
      <select v-model="statusFilter"
        class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="confirmed">Confirmed</option>
        <option value="processing">Processing</option>
        <option value="shipped">Shipped</option>
        <option value="delivered">Delivered</option>
        <option value="completed">Completed</option>
        <option value="cancelled">Cancelled</option>
      </select>
    </div>

    <!-- Empty state -->
    <div v-if="orders.data?.length === 0" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-[var(--color-text-secondary)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
      <p class="text-lg font-medium text-[var(--color-text)] mb-1">No orders found</p>
      <p class="text-sm text-[var(--color-text-secondary)] mb-4">{{ search || statusFilter ? 'Try adjusting your filters' : 'You haven\'t placed any orders yet.' }}</p>
      <a v-if="!search && !statusFilter" href="/" class="inline-block bg-accent text-white px-6 py-2.5 rounded-full font-semibold hover:bg-accent-hover transition-colors">Start Shopping</a>
    </div>

    <!-- Order cards -->
    <div v-else class="space-y-4">
      <div v-for="order in orders.data" :key="order.id"
        class="bg-white rounded-2xl border border-[var(--color-border)] p-5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
          <div>
            <h3 class="font-bold text-[var(--color-text)]">Order #{{ order.order_number }}</h3>
            <p class="text-xs text-[var(--color-text-secondary)]">{{ new Date(order.created_at).toLocaleDateString('en-NG', { year: 'numeric', month: 'short', day: 'numeric' }) }}</p>
          </div>
          <span class="self-start text-xs px-2.5 py-1 rounded-full font-medium"
            :class="{
              'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': order.status === 'completed' || order.status === 'delivered',
              'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': order.status === 'cancelled',
              'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': order.status === 'pending',
              'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400': order.status === 'processing' || order.status === 'shipped',
            }">
            {{ order.status }}
          </span>
        </div>

        <div class="flex items-center justify-between">
          <span class="text-lg font-bold text-accent">₦{{ Number(order.total_amount).toLocaleString() }}</span>
          <div class="flex gap-2">
            <a v-if="order.invoice && order.invoice.status !== 'paid'" :href="`/payment/${order.invoice.id}`"
              class="text-xs bg-accent text-white px-3 py-1.5 rounded-full hover:bg-accent-hover transition-colors">Upload Payment</a>
            <span v-if="order.invoice?.status === 'paid'"
              class="text-xs bg-green-100 text-green-700 px-3 py-1.5 rounded-full dark:bg-green-900/30 dark:text-green-400">Paid</span>
            <a :href="`/order-confirmation/${order.id}`"
              class="text-xs border border-[var(--color-border)] text-[var(--color-text)] px-3 py-1.5 rounded-full hover:border-accent transition-colors dark:border-gray-600">View</a>
          </div>
        </div>

        <div v-if="order.invoice" class="mt-3 pt-3 border-t border-[var(--color-border)] dark:border-gray-700 text-xs text-[var(--color-text-secondary)] flex flex-wrap gap-x-4 gap-y-1">
          <span>Invoice: {{ order.invoice.invoice_number }}</span>
          <span>Paid: ₦{{ Number(order.invoice.paid_amount || 0).toLocaleString() }}</span>
          <span>Due: <span :class="order.invoice.due_amount > 0 ? 'text-accent font-semibold' : 'text-green-600 dark:text-green-400'">₦{{ Number(order.invoice.due_amount || 0).toLocaleString() }}</span></span>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="orders.last_page > 1" class="flex justify-center gap-2 mt-6">
      <button v-for="p in orders.last_page" :key="p" @click="goToPage(p)"
        class="px-3 py-1 rounded-full text-sm font-medium transition-colors cursor-pointer"
        :class="p === orders.current_page ? 'bg-accent text-white' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'">
        {{ p }}
      </button>
    </div>
  </AccountLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AccountLayout from '@/Pages/Storefront/AccountLayout.vue'

const props = defineProps({
  orders: Object,
  customer: Object,
  cartCount: { type: Number, default: 0 },
  filters: Object,
})

const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')

function applyFilters() {
  router.get('/account/orders', {
    search: search.value || undefined,
    status: statusFilter.value || undefined,
  }, { preserveState: true, replace: true })
}

watch(search, () => applyFilters())
watch(statusFilter, () => applyFilters())

function goToPage(page) {
  router.get('/account/orders', {
    page,
    search: search.value || undefined,
    status: statusFilter.value || undefined,
  }, { preserveState: true, replace: true })
}
</script>
