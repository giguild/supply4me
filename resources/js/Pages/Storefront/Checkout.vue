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
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Shipping Address</h2>
            <form @submit.prevent="placeOrder">
              <div class="space-y-4">
                <div v-if="addresses.length > 0">
                  <label class="block text-sm font-medium text-[var(--color-text)] mb-2">Select a saved address *</label>
                  <div class="space-y-2">
                    <label
                      v-for="address in addresses"
                      :key="address.id"
                      class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors"
                      :class="form.shipping_address_id === address.id ? 'border-accent bg-accent/5' : 'border-[var(--color-border)] hover:border-accent/50 dark:border-gray-600'"
                    >
                      <input v-model="form.shipping_address_id" type="radio" :value="address.id" class="mt-0.5 text-accent focus:ring-accent" />
                      <div class="flex-1">
                        <div class="flex items-center gap-2">
                          <span class="font-medium text-sm text-[var(--color-text)]">{{ address.label }}</span>
                          <span v-if="address.is_default" class="text-xs bg-accent/10 text-accent px-2 py-0.5 rounded-full font-medium">Default</span>
                        </div>
                        <p class="text-sm text-[var(--color-text-secondary)]">{{ address.address_line_1 }}<span v-if="address.address_line_2">, {{ address.address_line_2 }}</span></p>
                        <p class="text-sm text-[var(--color-text-secondary)]">{{ [address.city, address.state, address.postal_code].filter(Boolean).join(', ') }}<span v-if="address.country">, {{ address.country }}</span></p>
                      </div>
                    </label>
                  </div>
                  <p v-if="formError" class="text-red-500 text-xs mt-1">{{ formError }}</p>
                </div>

                <div v-if="addresses.length === 0" class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                  <p class="text-sm text-amber-700 dark:text-amber-400 font-medium">You have no shipping addresses saved.</p>
                  <p class="text-xs text-amber-600 dark:text-amber-500 mt-1">Please <a href="/account" class="underline font-semibold">add a shipping address</a> in your account before placing an order.</p>
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

            <button @click="placeOrder" :disabled="processing || !form.shipping_address_id" class="w-full bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors disabled:opacity-50">
              {{ processing ? 'Placing Order...' : 'Place Order' }}
            </button>
            <p v-if="!form.shipping_address_id && addresses.length > 0" class="text-xs text-center text-amber-600 dark:text-amber-400 mt-2">Select a shipping address to continue</p>
            <p v-if="addresses.length === 0" class="text-xs text-center text-red-500 mt-2">Add a shipping address in your account first</p>
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
  addresses: { type: Array, default: () => [] },
  defaultAddress: Object,
  cartCount: { type: Number, default: 0 },
})

const form = reactive({
  shipping_address_id: props.defaultAddress?.id || '',
  notes: '',
})

const processing = ref(false)
const formError = ref('')

function placeOrder() {
  if (!form.shipping_address_id) {
    formError.value = 'Please select a shipping address'
    return
  }
  formError.value = ''
  processing.value = true
  router.post('/checkout/place-order', form, {
    onFinish: () => { processing.value = false },
    onError: (errors) => {
      formError.value = Object.values(errors)[0] || 'Failed to place order'
    },
  })
}
</script>
