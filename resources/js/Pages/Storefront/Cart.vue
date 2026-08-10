<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="cart">
    <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 overflow-x-hidden">
      <h1 class="text-2xl sm:text-3xl font-bold text-[var(--color-text)] mb-6 sm:mb-8">Shopping Cart</h1>

      <div v-if="items.length === 0" class="text-center py-16 bg-white rounded-2xl border border-[var(--color-border)] dark:bg-gray-800 dark:border-gray-700">
        <svg class="mx-auto h-16 w-16 text-[var(--color-text-secondary)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
        <h2 class="text-xl font-semibold text-[var(--color-text)] mb-2">Your cart is empty</h2>
        <p class="text-[var(--color-text-secondary)] mb-6">Start adding products to your cart.</p>
        <a href="/" class="inline-block bg-accent text-white px-6 py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors">Browse Products</a>
      </div>

      <template v-else>
        <!-- Cart Items -->
        <div class="space-y-4 mb-8">
          <div v-for="item in items" :key="item.key" class="bg-white rounded-2xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
            <!-- Mobile: stacked layout -->
            <div class="sm:hidden">
              <div class="flex items-start gap-3 mb-3">
                <div class="w-14 h-14 bg-accent-50 rounded-xl flex items-center justify-center flex-shrink-0 dark:bg-gray-700">
                  <span class="text-lg font-bold text-accent/40">{{ item.name.charAt(0) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="font-semibold text-[var(--color-text)] text-sm leading-tight">{{ item.name }}</h3>
                  <p class="text-xs text-[var(--color-text-secondary)] mt-0.5">{{ item.sku }}</p>
                  <p class="text-accent font-bold text-sm mt-1">₦{{ Number(item.price).toLocaleString() }}</p>
                </div>
                <p class="font-bold text-[var(--color-text)] text-sm whitespace-nowrap">₦{{ Number(item.line_total).toLocaleString() }}</p>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center border border-[var(--color-border)] rounded-full dark:border-gray-600">
                  <form @submit.prevent="updateQuantity(item, Math.max(item.minimum_order_quantity || 1, item.quantity - 1))">
                    <button type="submit" class="px-3 py-1.5 text-[var(--color-text)] hover:text-accent transition-colors text-sm">-</button>
                  </form>
                  <span class="px-3 text-sm font-medium text-[var(--color-text)]">{{ item.quantity }}</span>
                  <form @submit.prevent="updateQuantity(item, item.maximum_order_quantity ? Math.min(item.maximum_order_quantity, item.quantity + 1) : item.quantity + 1)">
                    <button type="submit" class="px-3 py-1.5 text-[var(--color-text)] hover:text-accent transition-colors text-sm">+</button>
                  </form>
                </div>
                <form @submit.prevent="removeItem(item)">
                  <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors font-medium">Remove</button>
                </form>
              </div>
            </div>

            <!-- Desktop: horizontal layout -->
            <div class="hidden sm:flex items-center gap-4">
              <div class="w-16 h-16 bg-accent-50 rounded-xl flex items-center justify-center flex-shrink-0 dark:bg-gray-700">
                <span class="text-xl font-bold text-accent/40">{{ item.name.charAt(0) }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-[var(--color-text)] truncate">{{ item.name }}</h3>
                <p class="text-sm text-[var(--color-text-secondary)]">SKU: {{ item.sku }}</p>
                <p class="text-accent font-bold mt-1">₦{{ Number(item.price).toLocaleString() }}</p>
              </div>
              <div class="flex items-center border border-[var(--color-border)] rounded-full dark:border-gray-600">
                <form @submit.prevent="updateQuantity(item, Math.max(item.minimum_order_quantity || 1, item.quantity - 1))">
                  <button type="submit" class="px-3 py-1 text-[var(--color-text)] hover:text-accent transition-colors">-</button>
                </form>
                <span class="px-3 text-sm font-medium text-[var(--color-text)]">{{ item.quantity }}</span>
                <form @submit.prevent="updateQuantity(item, item.maximum_order_quantity ? Math.min(item.maximum_order_quantity, item.quantity + 1) : item.quantity + 1)">
                  <button type="submit" class="px-3 py-1 text-[var(--color-text)] hover:text-accent transition-colors">+</button>
                </form>
              </div>
              <div class="text-right">
                <p class="font-bold text-[var(--color-text)]">₦{{ Number(item.line_total).toLocaleString() }}</p>
                <form @submit.prevent="removeItem(item)">
                  <button type="submit" class="text-xs text-red-500 hover:text-red-700 mt-1 transition-colors">Remove</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
          <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Order Summary</h2>
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
          <a href="/checkout" class="block w-full bg-accent text-white text-center py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors">
            Proceed to Checkout
          </a>
        </div>
      </template>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  items: Array,
  subtotal: Number,
  taxRate: Number,
  taxAmount: Number,
  total: Number,
  cartCount: { type: Number, default: 0 },
})

function updateQuantity(item, newQty) {
  if (newQty < 1) {
    removeItem(item)
    return
  }
  router.post('/cart/update', {
    product_id: item.product_id,
    quantity: newQty,
  }, { preserveState: true })
}

function removeItem(item) {
  router.post('/cart/remove', {
    product_id: item.product_id,
  }, { preserveState: true })
}
</script>
