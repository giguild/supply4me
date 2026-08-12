<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="account">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
          <svg class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h1 class="text-3xl font-bold text-[var(--color-text)]">Order Confirmed!</h1>
        <p class="text-[var(--color-text-secondary)] mt-2">Thank you for your order. We'll process it shortly.</p>
      </div>

      <!-- Order Details -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
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
      </div>

      <!-- Invoice & Payment Status -->
      <div v-if="invoice" class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-[var(--color-border)]">
          <h3 class="font-semibold text-[var(--color-text)]">Invoice {{ invoice.invoice_number }}</h3>
          <span class="badge" :class="{
            'badge-success': invoice.status === 'paid',
            'badge-warning': invoice.status === 'partial',
            'badge-danger': invoice.status === 'overdue',
          }">{{ invoice.status }}</span>
        </div>

        <div class="space-y-2 text-sm mb-4">
          <div class="flex justify-between">
            <span class="text-[var(--color-text-secondary)]">Total</span>
            <span class="font-medium text-[var(--color-text)]">₦{{ Number(invoice.total_amount).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-[var(--color-text-secondary)]">Paid</span>
            <span class="font-medium text-green-600 dark:text-green-400">₦{{ Number(invoice.paid_amount).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between border-t border-[var(--color-border)] pt-2">
            <span class="text-[var(--color-text-secondary)]">Balance Due</span>
            <span class="font-bold" :class="invoice.due_amount > 0 ? 'text-accent' : 'text-green-600 dark:text-green-400'">₦{{ Number(invoice.due_amount).toLocaleString() }}</span>
          </div>
        </div>

        <!-- Payment History -->
        <div v-if="invoice.payments?.length" class="border-t border-[var(--color-border)] pt-4">
          <h4 class="font-medium text-[var(--color-text)] mb-3">Payment History</h4>
          <div v-for="pmt in invoice.payments" :key="pmt.id" class="flex items-center justify-between text-sm py-2 border-b border-[var(--color-border)] last:border-0">
            <div>
              <span class="text-[var(--color-text)]">{{ pmt.payment_number }}</span>
              <span class="text-[var(--color-text-secondary)] ml-2">{{ new Date(pmt.payment_date).toLocaleDateString() }}</span>
            </div>
            <div class="text-right">
              <span class="font-medium text-[var(--color-text)]">₦{{ Number(pmt.amount).toLocaleString() }}</span>
              <span class="text-xs px-2 py-0.5 rounded-full ml-2" :class="{
                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': pmt.status === 'pending',
                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': pmt.status === 'completed',
                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': pmt.status === 'rejected',
              }">{{ pmt.status }}</span>
            </div>
          </div>
        </div>

        <!-- Upload Link -->
        <div v-if="invoice.status !== 'paid'" class="border-t border-[var(--color-border)] pt-4 mt-2">
          <p class="text-sm text-[var(--color-text-secondary)]">
            Balance of ₦{{ Number(invoice.due_amount).toLocaleString() }} remaining.
            <a :href="`/payment/${invoice.id}`" class="text-accent font-semibold hover:text-accent-hover ml-1">Upload payment receipt</a>
          </p>
        </div>
        <div v-else class="border-t border-[var(--color-border)] pt-4 mt-2">
          <p class="text-sm text-green-600 dark:text-green-400 font-medium">This invoice has been fully paid.</p>
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
  invoice: Object,
  cartCount: { type: Number, default: 0 },
})
</script>
