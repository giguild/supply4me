<template>
  <StorefrontLayout :cartCount="cartCount" :customer="customer" currentPage="account">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Customer Info -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-[var(--color-text)] mb-4">My Account</h1>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-[var(--color-text-secondary)]">Name</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.name }}</p>
          </div>
          <div>
            <span class="text-[var(--color-text-secondary)]">Email</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.email }}</p>
          </div>
          <div>
            <span class="text-[var(--color-text-secondary)]">Phone</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.phone || 'N/A' }}</p>
          </div>
          <div>
            <span class="text-[var(--color-text-secondary)]">Customer #</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.customer_number }}</p>
          </div>
        </div>
      </div>

      <!-- Orders -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">My Orders</h2>

        <div v-if="orders.data?.length === 0" class="text-center py-8">
          <p class="text-[var(--color-text-secondary)]">No orders yet.</p>
          <a href="/" class="text-accent font-semibold hover:text-accent-hover mt-2 inline-block">Start Shopping</a>
        </div>

        <div v-else class="space-y-4">
          <div v-for="order in orders.data" :key="order.id" class="border border-[var(--color-border)] rounded-xl p-4 dark:border-gray-600">
            <div class="flex items-center justify-between mb-2">
              <div>
                <h3 class="font-semibold text-[var(--color-text)]">Order #{{ order.order_number }}</h3>
                <p class="text-xs text-[var(--color-text-secondary)]">{{ new Date(order.created_at).toLocaleDateString() }}</p>
              </div>
              <span class="badge" :class="order.status === 'completed' ? 'badge-success' : order.status === 'cancelled' ? 'badge-danger' : 'badge-warning'">
                {{ order.status }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="font-bold text-accent">₦{{ Number(order.total_amount).toLocaleString() }}</span>
              <div class="flex gap-2">
                <a v-if="order.payment_status === 'unpaid'" :href="`/payment/${order.id}`" class="text-xs bg-accent text-white px-3 py-1 rounded-full hover:bg-accent-hover transition-colors">Upload Payment</a>
                <a :href="`/order-confirmation/${order.id}`" class="text-xs border border-[var(--color-border)] text-[var(--color-text)] px-3 py-1 rounded-full hover:border-accent transition-colors dark:border-gray-600">View</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="orders.last_page > 1" class="flex justify-center gap-2 mt-6">
          <a
            v-for="page in orders.last_page"
            :key="page"
            :href="`?page=${page}`"
            class="px-3 py-1 rounded-full text-sm font-medium transition-colors"
            :class="page === orders.current_page ? 'bg-accent text-white' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'"
          >
            {{ page }}
          </a>
        </div>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  customer: Object,
  orders: Object,
  cartCount: { type: Number, default: 0 },
})
</script>
