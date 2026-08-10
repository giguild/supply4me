<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="account">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Success Checkmark -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
          <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-bold text-[var(--color-text)]">Order Confirmed!</h1>
        <p class="text-[var(--color-text-secondary)] mt-2">Thank you for your order. We'll process it shortly.</p>
      </div>

      <!-- Order Details -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex justify-between items-center mb-6 pb-4 border-b border-[var(--color-border)]">
          <div>
            <h2 class="text-lg font-bold text-[var(--color-text)]">Order #{{ order.order_number }}</h2>
            <p class="text-sm text-[var(--color-text-secondary)]">{{ new Date(order.created_at).toLocaleDateString() }}</p>
          </div>
          <span class="badge badge-warning">{{ order.status }}</span>
        </div>

        <div class="space-y-3 mb-6">
          <div class="flex justify-between text-sm">
            <span class="text-[var(--color-text-secondary)]">Subtotal</span>
            <span class="font-medium text-[var(--color-text)]">₦{{ Number(order.subtotal).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-[var(--color-text-secondary)]">Tax</span>
            <span class="font-medium text-[var(--color-text)]">₦{{ Number(order.tax_amount).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-lg font-bold border-t border-[var(--color-border)] pt-3">
            <span class="text-[var(--color-text)]">Total</span>
            <span class="text-accent">₦{{ Number(order.total_amount).toLocaleString() }}</span>
          </div>
        </div>

        <div v-if="order.payments?.length" class="border-t border-[var(--color-border)] pt-4">
          <h3 class="font-semibold text-[var(--color-text)] mb-2">Payment Status</h3>
          <div v-for="payment in order.payments" :key="payment.id" class="flex items-center justify-between text-sm">
            <span class="text-[var(--color-text-secondary)]">Payment #{{ payment.payment_number }}</span>
            <span :class="payment.status === 'approved' ? 'text-green-600' : payment.status === 'rejected' ? 'text-red-600' : 'text-yellow-600'" class="font-medium">
              {{ payment.status }} - ₦{{ Number(payment.amount).toLocaleString() }}
            </span>
          </div>
        </div>

        <div v-else class="border-t border-[var(--color-border)] pt-4">
          <p class="text-sm text-[var(--color-text-secondary)]">No payment uploaded yet. <a :href="`/payment/${order.id}`" class="text-accent font-semibold hover:text-accent-hover">Upload payment receipt</a></p>
        </div>
      </div>

      <div class="flex gap-4 mt-6">
        <a href="/" class="flex-1 text-center bg-white border border-[var(--color-border)] text-[var(--color-text)] py-3 rounded-full font-semibold hover:border-accent transition-colors dark:bg-gray-800 dark:border-gray-600 dark:text-white">Continue Shopping</a>
        <a href="/account" class="flex-1 text-center bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors">View Account</a>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  order: Object,
  cartCount: { type: Number, default: 0 },
})
</script>
