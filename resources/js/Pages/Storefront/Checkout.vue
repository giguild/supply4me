<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="home">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-[var(--color-text)] mb-8">Checkout</h1>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Order Summary -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Order Items</h2>
            <div class="divide-y divide-[var(--color-border)]">
              <div v-for="item in items" :key="item.product_id" class="py-3 flex items-center justify-between">
                <div>
                  <p class="font-medium text-[var(--color-text)]">{{ item.name }}</p>
                  <p class="text-sm text-[var(--color-text-secondary)]">{{ item.quantity }} x ₦{{ Number(item.price).toLocaleString() }}</p>
                </div>
                <p class="font-bold text-[var(--color-text)]">₦{{ Number(item.line_total).toLocaleString() }}</p>
              </div>
            </div>
          </div>

          <!-- Shipping / Notes -->
          <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mt-4 dark:bg-gray-800 dark:border-gray-700">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Additional Details</h2>
            <form @submit.prevent="placeOrder">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Shipping Address (optional)</label>
                  <textarea v-model="form.shipping_address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Delivery address..."></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Notes (optional)</label>
                  <textarea v-model="form.notes" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Any special instructions..."></textarea>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Summary -->
        <div>
          <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 sticky top-20 dark:bg-gray-800 dark:border-gray-700">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Summary</h2>
            <div class="space-y-3 mb-6">
              <div class="flex justify-between text-sm">
                <span class="text-[var(--color-text-secondary)]">Subtotal</span>
                <span class="font-medium text-[var(--color-text)]">₦{{ Number(subtotal).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-[var(--color-text-secondary)]">Tax ({{ taxRate }}%)</span>
                <span class="font-medium text-[var(--color-text)]">₦{{ Number(taxAmount).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold border-t border-[var(--color-border)] pt-3">
                <span class="text-[var(--color-text)]">Total</span>
                <span class="text-accent">₦{{ Number(total).toLocaleString() }}</span>
              </div>
            </div>

            <div class="mb-4 p-3 bg-accent-50 rounded-xl text-sm dark:bg-gray-700">
              <p class="text-[var(--color-text)]"><strong>Customer:</strong> {{ customer?.name }}</p>
              <p class="text-[var(--color-text-secondary)]">{{ customer?.email }}</p>
            </div>

            <button @click="placeOrder" :disabled="processing" class="w-full bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors disabled:opacity-50">
              {{ processing ? 'Placing Order...' : 'Place Order' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  items: Array,
  subtotal: Number,
  taxRate: Number,
  taxAmount: Number,
  total: Number,
  customer: Object,
  cartCount: { type: Number, default: 0 },
})

const form = reactive({
  shipping_address: '',
  notes: '',
})

const processing = ref(false)

function placeOrder() {
  processing.value = true
  router.post('/checkout/place-order', form, {
    onFinish: () => { processing.value = false },
  })
}
</script>
