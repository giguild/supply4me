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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Checkout</h1>
              <p class="text-white/60 text-sm">Review your order and confirm delivery</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Order + Shipping -->
            <div class="lg:col-span-2 space-y-6">
              <!-- Order Items -->
              <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6">
                <h2 class="text-lg font-bold text-[#2D2C2C] mb-5 flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                  Order Items
                </h2>
                <div class="space-y-4">
                  <div v-for="item in items" :key="item.product_id" class="flex items-center gap-4 p-3 bg-[#F1EFEE] rounded-xl">
                    <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shrink-0">
                      <span class="text-lg font-bold text-[#9F5124]/40">{{ item.name.charAt(0) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="font-semibold text-[#2D2C2C] text-sm truncate">{{ item.name }}</p>
                      <p class="text-xs text-[#616262]">{{ item.quantity }} x ₦{{ Number(item.price).toLocaleString() }}</p>
                    </div>
                    <p class="font-bold text-[#2D2C2C] text-sm shrink-0">₦{{ Number(item.line_total).toLocaleString() }}</p>
                  </div>
                </div>
              </div>

              <!-- Shipping Address -->
              <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6">
                <h2 class="text-lg font-bold text-[#2D2C2C] mb-5 flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  Shipping Address
                </h2>

                <form @submit.prevent="placeOrder">
                  <div class="space-y-4">
                    <!-- Saved Addresses -->
                    <div v-if="addresses.length > 0">
                      <label class="block text-sm font-semibold text-[#2D2C2C] mb-3">Select a saved address *</label>
                      <div class="space-y-3">
                        <label
                          v-for="address in addresses"
                          :key="address.id"
                          class="flex items-start gap-3 p-4 rounded-xl border cursor-pointer transition-all duration-300"
                          :class="form.shipping_address_id === address.id
                            ? 'border-[#9F5124] bg-[#9F5124]/5 shadow-sm'
                            : 'border-[#2D2C2C]/10 hover:border-[#9F5124]/40'"
                        >
                          <input v-model="form.shipping_address_id" type="radio" :value="address.id" class="mt-0.5 text-[#9F5124] focus:ring-[#9F5124]" />
                          <div class="flex-1">
                            <div class="flex items-center gap-2">
                              <span class="font-semibold text-sm text-[#2D2C2C]">{{ address.label }}</span>
                              <span v-if="address.is_default" class="text-xs bg-[#9F5124]/10 text-[#9F5124] px-2 py-0.5 rounded-full font-semibold">Default</span>
                            </div>
                            <p class="text-sm text-[#616262] mt-1">{{ address.address_line_1 }}<span v-if="address.address_line_2">, {{ address.address_line_2 }}</span></p>
                            <p class="text-sm text-[#616262]">{{ [address.city, address.state, address.postal_code].filter(Boolean).join(', ') }}<span v-if="address.country">, {{ address.country }}</span></p>
                          </div>
                        </label>
                      </div>
                      <p v-if="formError" class="text-red-500 text-xs mt-2 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ formError }}
                      </p>
                    </div>

                    <!-- No Addresses -->
                    <div v-if="addresses.length === 0" class="p-5 bg-amber-50 rounded-xl border border-amber-200">
                      <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                          <p class="text-sm text-amber-700 font-semibold">No shipping addresses saved.</p>
                          <p class="text-xs text-amber-600 mt-1">Please <a href="/account" class="underline font-semibold hover:text-amber-800">add a shipping address</a> in your account before placing an order.</p>
                        </div>
                      </div>
                    </div>

                    <!-- Notes -->
                    <div>
                      <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Notes (optional)</label>
                      <textarea
                        v-model="form.notes"
                        rows="3"
                        class="w-full px-4 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] placeholder-[#616262] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm"
                        placeholder="Any special instructions for delivery..."
                      ></textarea>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Right: Summary -->
            <div class="lg:col-span-1">
              <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 sticky top-24">
                <h2 class="text-lg font-bold text-[#2D2C2C] mb-5 flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                  Summary
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

                <!-- Customer Info -->
                <div class="p-4 bg-[#F1EFEE] rounded-xl mb-5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#9F5124]/10 flex items-center justify-center text-[#9F5124] font-bold text-sm shrink-0">
                      {{ customer?.name?.charAt(0) || '?' }}
                    </div>
                    <div class="min-w-0">
                      <p class="font-semibold text-[#2D2C2C] text-sm truncate">{{ customer?.name }}</p>
                      <p class="text-xs text-[#616262] truncate">{{ customer?.email }}</p>
                    </div>
                  </div>
                </div>

                <button
                  @click="placeOrder"
                  :disabled="processing || !form.shipping_address_id"
                  class="w-full bg-[#9F5124] text-white py-3.5 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                  <span v-if="processing" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  {{ processing ? 'Placing Order...' : 'Place Order' }}
                </button>

                <p v-if="!form.shipping_address_id && addresses.length > 0" class="text-xs text-center text-amber-600 mt-3 flex items-center justify-center gap-1">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Select a shipping address to continue
                </p>
                <p v-if="addresses.length === 0" class="text-xs text-center text-red-500 mt-3">
                  Add a shipping address in your account first
                </p>
              </div>
            </div>

          </div>
        </div>
      </section>
    </main>

    <MarketingFooter :customer="$page.props.auth?.customer" :cartCount="cartCount" />
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

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
