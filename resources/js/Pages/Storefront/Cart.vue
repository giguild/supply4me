<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <!-- Hero Banner -->
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-[#9F5124]/20 flex items-center justify-center">
              <svg class="w-6 h-6 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Shopping Cart</h1>
              <p class="text-white/60 text-sm">{{ items.length }} {{ items.length === 1 ? 'item' : 'items' }} in your cart</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

          <!-- Empty State -->
          <div v-if="items.length === 0" class="text-center py-20 bg-white rounded-3xl border border-[#2D2C2C]/5">
            <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-[#9F5124]/10 flex items-center justify-center">
              <svg class="w-10 h-10 text-[#9F5124]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-[#2D2C2C] mb-2">Your cart is empty</h2>
            <p class="text-[#616262] mb-8">Start adding products to your cart.</p>
            <a href="/shop" class="inline-flex items-center gap-2 bg-[#9F5124] text-white px-8 py-3.5 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
              </svg>
              Browse Products
            </a>
          </div>

          <template v-else>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
              <!-- Cart Items -->
              <div class="lg:col-span-2 space-y-4">
                <div v-for="item in items" :key="item.key" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-5 hover:shadow-md transition-all duration-300">
                  <!-- Mobile -->
                  <div class="sm:hidden">
                    <div class="flex items-start gap-3 mb-3">
                      <div class="w-14 h-14 bg-[#F1EFEE] rounded-xl flex items-center justify-center shrink-0">
                        <span class="text-lg font-bold text-[#9F5124]/40">{{ item.name.charAt(0) }}</span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-[#2D2C2C] text-sm leading-tight">{{ item.name }}</h3>
                        <p class="text-xs text-[#616262] mt-0.5">{{ item.sku }}</p>
                        <p class="text-[#9F5124] font-bold text-sm mt-1">₦{{ Number(item.price).toLocaleString() }}</p>
                      </div>
                      <p class="font-bold text-[#2D2C2C] text-sm whitespace-nowrap">₦{{ Number(item.line_total).toLocaleString() }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                      <div class="flex items-center border border-[#2D2C2C]/10 rounded-full overflow-hidden">
                        <form @submit.prevent="updateQuantity(item, Math.max(item.minimum_order_quantity || 1, item.quantity - 1))">
                          <button type="submit" class="px-3 py-1.5 text-[#2D2C2C] hover:bg-[#F1EFEE] transition-colors text-sm">&minus;</button>
                        </form>
                        <span class="px-3 text-sm font-bold text-[#2D2C2C]">{{ item.quantity }} <span class="font-normal text-[#616262]">{{ item.unit?.short_name || 'pc' }}</span></span>
                        <form @submit.prevent="updateQuantity(item, item.maximum_order_quantity ? Math.min(item.maximum_order_quantity, item.quantity + 1) : item.quantity + 1)">
                          <button type="submit" class="px-3 py-1.5 text-[#2D2C2C] hover:bg-[#F1EFEE] transition-colors text-sm">&plus;</button>
                        </form>
                      </div>
                      <form @submit.prevent="removeItem(item)">
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors font-medium flex items-center gap-1">
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                          Remove
                        </button>
                      </form>
                    </div>
                  </div>

                  <!-- Desktop -->
                  <div class="hidden sm:flex items-center gap-4">
                    <div class="w-16 h-16 bg-[#F1EFEE] rounded-xl flex items-center justify-center shrink-0">
                      <span class="text-xl font-bold text-[#9F5124]/40">{{ item.name.charAt(0) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <h3 class="font-semibold text-[#2D2C2C] truncate">{{ item.name }}</h3>
                      <p class="text-sm text-[#616262]">SKU: {{ item.sku }}</p>
                      <p class="text-[#9F5124] font-bold mt-1">₦{{ Number(item.price).toLocaleString() }}</p>
                    </div>
                    <div class="flex items-center border border-[#2D2C2C]/10 rounded-full overflow-hidden">
                      <form @submit.prevent="updateQuantity(item, Math.max(item.minimum_order_quantity || 1, item.quantity - 1))">
                        <button type="submit" class="px-3 py-1.5 text-[#2D2C2C] hover:bg-[#F1EFEE] transition-colors">&minus;</button>
                      </form>
                      <span class="px-3 text-sm font-bold text-[#2D2C2C]">{{ item.quantity }} <span class="font-normal text-[#616262]">{{ item.unit?.short_name || 'pc' }}</span></span>
                      <form @submit.prevent="updateQuantity(item, item.maximum_order_quantity ? Math.min(item.maximum_order_quantity, item.quantity + 1) : item.quantity + 1)">
                        <button type="submit" class="px-3 py-1.5 text-[#2D2C2C] hover:bg-[#F1EFEE] transition-colors">&plus;</button>
                      </form>
                    </div>
                    <div class="text-right shrink-0">
                      <p class="font-bold text-[#2D2C2C]">₦{{ Number(item.line_total).toLocaleString() }}</p>
                      <form @submit.prevent="removeItem(item)">
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 mt-1 transition-colors font-medium">Remove</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Summary -->
              <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 sticky top-24">
                  <h2 class="text-lg font-bold text-[#2D2C2C] mb-5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Order Summary
                  </h2>

                  <div class="space-y-3 mb-5">
                    <div class="flex justify-between text-sm">
                      <span class="text-[#616262]">Subtotal</span>
                      <span class="font-semibold text-[#2D2C2C]">₦{{ Number(subtotal).toLocaleString() }}</span>
                    </div>
                    <div v-if="taxAmount > 0" class="flex justify-between text-sm">
                      <span class="text-[#616262]">Tax ({{ taxRate }}%)</span>
                      <span class="font-semibold text-[#2D2C2C]">₦{{ Number(taxAmount).toLocaleString() }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-[#2D2C2C]/5 pt-4">
                      <span class="text-[#2D2C2C]">Total</span>
                      <span class="text-[#9F5124]">₦{{ Number(total).toLocaleString() }}</span>
                    </div>
                  </div>

                  <a href="/checkout" class="block w-full bg-[#9F5124] text-white text-center py-3.5 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    Proceed to Checkout
                  </a>

                  <a href="/shop" class="block w-full text-center text-[#9F5124] font-semibold text-sm mt-4 hover:text-[#8a4620] transition-colors">
                    Continue Shopping
                  </a>
                </div>
              </div>
            </div>
          </template>
        </div>
      </section>
    </main>

    <MarketingFooter :customer="$page.props.auth?.customer" :cartCount="cartCount" />
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

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
