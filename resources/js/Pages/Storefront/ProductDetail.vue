<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <!-- Breadcrumb Bar -->
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-24 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <nav class="flex items-center gap-2 text-sm">
            <a href="/" class="text-white/50 hover:text-white transition-colors">Home</a>
            <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="/shop" class="text-white/50 hover:text-white transition-colors">Shop</a>
            <svg class="w-4 h-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white/80 truncate max-w-[200px]">{{ product.name }}</span>
          </nav>
        </div>
      </section>

      <!-- Product Content -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14">

            <!-- Left: Images -->
            <div>
              <!-- Main Image -->
              <div class="bg-white rounded-3xl border border-[#2D2C2C]/5 overflow-hidden aspect-square flex items-center justify-center shadow-sm">
                <img
                  v-if="selectedImage"
                  :src="selectedImage"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
                <div v-else class="flex flex-col items-center gap-4">
                  <div class="w-24 h-24 rounded-2xl bg-[#9F5124]/10 flex items-center justify-center">
                    <span class="text-5xl font-bold text-[#9F5124]/30">{{ product.name.charAt(0) }}</span>
                  </div>
                  <p class="text-sm text-[#616262]">No image available</p>
                </div>
              </div>

              <!-- Thumbnails -->
              <div v-if="productImages.length > 1" class="flex gap-3 mt-4">
                <button
                  v-for="(img, idx) in productImages"
                  :key="idx"
                  @click="selectedImage = img"
                  class="w-20 h-20 rounded-xl border-2 overflow-hidden transition-all duration-300"
                  :class="selectedImage === img
                    ? 'border-[#9F5124] shadow-md shadow-[#9F5124]/20'
                    : 'border-[#2D2C2C]/5 hover:border-[#9F5124]/40'"
                >
                  <img :src="img" class="w-full h-full object-cover" />
                </button>
              </div>
            </div>

            <!-- Right: Info -->
            <div class="lg:py-4">
              <!-- Badges -->
              <div class="flex items-center gap-2 mb-4">
                <span
                  v-if="product.category"
                  class="inline-flex items-center gap-1 bg-[#9F5124]/10 text-[#9F5124] text-xs font-semibold px-3 py-1.5 rounded-full"
                >
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  {{ product.category.name }}
                </span>
                <span
                  v-if="product.brand"
                  class="inline-flex items-center gap-1 bg-[#2D2C2C]/5 text-[#616262] text-xs font-semibold px-3 py-1.5 rounded-full"
                >
                  {{ product.brand.name }}
                </span>
              </div>

              <!-- Name -->
              <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#2D2C2C] mb-2 leading-tight">
                {{ product.name }}
              </h1>

              <!-- SKU -->
              <p class="text-sm text-[#616262] mb-5">SKU: {{ product.sku }}</p>

              <!-- Price -->
              <div class="flex items-baseline gap-3 mb-6">
                <span class="text-3xl sm:text-4xl font-extrabold text-[#9F5124]">
                  ₦{{ Number(product.selling_price).toLocaleString() }}
                </span>
                <span class="text-sm text-[#616262]">
                  per {{ product.unit?.short_name || 'unit' }}
                </span>
              </div>

              <!-- Description -->
              <p v-if="product.description" class="text-[#616262] leading-relaxed mb-8">
                {{ product.description }}
              </p>

              <!-- Details Grid -->
              <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white rounded-xl p-4 border border-[#2D2C2C]/5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-[#9F5124]/10 flex items-center justify-center">
                      <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-xs text-[#616262]">Unit</p>
                      <p class="font-semibold text-[#2D2C2C] text-sm">{{ product.unit?.name || 'N/A' }}</p>
                    </div>
                  </div>
                </div>

                <div class="bg-white rounded-xl p-4 border border-[#2D2C2C]/5">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                      :class="product.status === 'active' ? 'bg-green-50' : 'bg-red-50'">
                      <svg class="w-5 h-5" :class="product.status === 'active' ? 'text-green-600' : 'text-red-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-xs text-[#616262]">Availability</p>
                      <p class="font-semibold text-sm" :class="product.status === 'active' ? 'text-green-600' : 'text-red-600'">
                        {{ product.status === 'active' ? 'In Stock' : 'Out of Stock' }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Add to Cart -->
              <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 mb-6">
                <form @submit.prevent="addToCart" class="flex items-center gap-4">
                  <!-- Quantity -->
                  <div class="flex items-center border border-[#2D2C2C]/10 rounded-full overflow-hidden">
                    <button
                      type="button"
                      @click="quantity = Math.max(minQty, quantity - 1)"
                      class="w-11 h-11 flex items-center justify-center text-[#2D2C2C] hover:bg-[#F1EFEE] transition-colors text-lg font-medium"
                    >
                      &minus;
                    </button>
                    <input
                      v-model.number="quantity"
                      type="number"
                      :min="minQty"
                      :max="maxQty || undefined"
                      class="w-14 text-center border-0 bg-transparent text-[#2D2C2C] font-bold text-lg focus:ring-0"
                    />
                    <button
                      type="button"
                      @click="quantity = maxQty ? Math.min(maxQty, quantity + 1) : quantity + 1"
                      class="w-11 h-11 flex items-center justify-center text-[#2D2C2C] hover:bg-[#F1EFEE] transition-colors text-lg font-medium"
                    >
                      &plus;
                    </button>
                  </div>

                  <!-- Add Button -->
                  <button
                    type="submit"
                    :disabled="adding"
                    class="flex-1 bg-[#9F5124] text-white py-3.5 rounded-full font-bold text-base hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25 disabled:opacity-50 flex items-center justify-center gap-2"
                  >
                    <svg v-if="!adding" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span v-if="adding" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    {{ adding ? 'Adding...' : 'Add to Cart' }}
                  </button>

                  <!-- Wishlist -->
                  <button
                    v-if="$page.props.auth?.customer"
                    type="button"
                    @click="toggleWishlist"
                    class="w-12 h-12 rounded-full border border-[#2D2C2C]/10 flex items-center justify-center hover:border-red-300 hover:bg-red-50 transition-all duration-300"
                  >
                    <svg
                      class="h-5 w-5 transition-colors"
                      :class="isWishlisted ? 'text-red-500 fill-red-500' : 'text-[#616262]'"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                  </button>
                </form>

                <!-- Order Info -->
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-[#2D2C2C]/5 text-xs text-[#616262]">
                  <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Min: {{ minQty }} {{ product.unit?.short_name || 'pc' }}
                  </span>
                  <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                    Max: {{ maxQty ? maxQty + ' ' + (product.unit?.short_name || 'pc') : 'No limit' }}
                  </span>
                </div>

                <!-- Messages -->
                <p v-if="message" class="mt-3 text-sm text-green-600 font-medium flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ message }}
                </p>
                <p v-if="error" class="mt-3 text-sm text-red-600 font-medium flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ error }}
                </p>
              </div>

              <!-- Trust Signals -->
              <div class="grid grid-cols-3 gap-3">
                <div class="bg-white rounded-xl p-3 border border-[#2D2C2C]/5 text-center">
                  <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-[#9F5124]/10 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                  </div>
                  <p class="text-xs font-semibold text-[#2D2C2C]">Genuine</p>
                  <p class="text-[10px] text-[#616262]">Products</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-[#2D2C2C]/5 text-center">
                  <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-[#9F5124]/10 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                    </svg>
                  </div>
                  <p class="text-xs font-semibold text-[#2D2C2C]">Reliable</p>
                  <p class="text-[10px] text-[#616262]">Delivery</p>
                </div>
                <div class="bg-white rounded-xl p-3 border border-[#2D2C2C]/5 text-center">
                  <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-[#9F5124]/10 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                  </div>
                  <p class="text-xs font-semibold text-[#2D2C2C]">Secure</p>
                  <p class="text-[10px] text-[#616262]">Payment</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Variants -->
          <div v-if="product.variants?.length" class="mt-14">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 rounded-xl bg-[#9F5124]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
              </div>
              <h2 class="text-2xl font-extrabold text-[#2D2C2C]">Available Variants</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="variant in product.variants"
                :key="variant.id"
                class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group"
              >
                <div class="flex items-start justify-between mb-3">
                  <div>
                    <h3 class="font-bold text-[#2D2C2C] group-hover:text-[#9F5124] transition-colors">{{ variant.name }}</h3>
                    <p class="text-xs text-[#616262] mt-1">SKU: {{ variant.sku }}</p>
                  </div>
                  <span class="text-lg font-extrabold text-[#9F5124]">
                    ₦{{ Number(variant.selling_price || product.selling_price).toLocaleString() }}
                  </span>
                </div>
                <button
                  @click="addToCartVariant(variant)"
                  class="w-full mt-3 py-2.5 rounded-full border border-[#9F5124]/30 text-[#9F5124] text-sm font-semibold hover:bg-[#9F5124] hover:text-white transition-all duration-300"
                >
                  Add to Cart
                </button>
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
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

const props = defineProps({
  product: Object,
  cartCount: { type: Number, default: 0 },
  wishlistIds: { type: Array, default: () => [] },
  company: Object,
})

const productImages = computed(() => {
  return (props.product.product_images || []).map(p => `/storage/${p}`)
})

const selectedImage = ref(productImages.value[0] || null)

const quantity = ref(1)
const adding = ref(false)
const message = ref('')
const error = ref('')
const isWishlisted = ref(props.wishlistIds.includes(props.product.id))

const minQty = props.product.minimum_order_quantity || 1
const maxQty = props.product.maximum_order_quantity || null

quantity.value = minQty

function addToCart() {
  adding.value = true
  message.value = ''
  error.value = ''

  router.post('/cart/add', {
    product_id: props.product.id,
    quantity: quantity.value,
  }, {
    preserveState: true,
    onFinish: () => {
      adding.value = false
      message.value = 'Added to cart!'
    },
    onError: (errors) => {
      error.value = Object.values(errors)[0] || 'Failed to add to cart'
    },
  })
}

function addToCartVariant(variant) {
  quantity.value = minQty
  addToCart()
}

function toggleWishlist() {
  fetch('/wishlist/toggle', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || ''),
    },
    body: JSON.stringify({ product_id: props.product.id }),
  })
    .then(res => res.json())
    .then(data => {
      isWishlisted.value = data.added
    })
}
</script>
