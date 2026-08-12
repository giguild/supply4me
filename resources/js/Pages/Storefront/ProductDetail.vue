<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="home">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center gap-2 text-sm text-[var(--color-text-secondary)] mb-6">
        <a href="/" class="hover:text-accent transition-colors">Home</a>
        <span>/</span>
        <span class="text-[var(--color-text)]">{{ product.name }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Image -->
        <div class="bg-white rounded-2xl border border-[var(--color-border)] aspect-square flex items-center justify-center dark:bg-gray-800 dark:border-gray-700">
          <span class="text-8xl font-bold text-accent/20">{{ product.name.charAt(0) }}</span>
        </div>

        <!-- Product Info -->
        <div>
          <div class="flex items-center gap-2 mb-3">
            <span v-if="product.category" class="badge badge-purple">{{ product.category.name }}</span>
            <span v-if="product.brand" class="badge badge-gray">{{ product.brand.name }}</span>
          </div>

          <h1 class="text-3xl font-bold text-[var(--color-text)] mb-2">{{ product.name }}</h1>
          <p class="text-sm text-[var(--color-text-secondary)] mb-4">SKU: {{ product.sku }}</p>

          <div class="text-3xl font-bold text-accent mb-6">₦{{ Number(product.selling_price).toLocaleString() }}</div>

          <p v-if="product.description" class="text-[var(--color-text-secondary)] mb-8 leading-relaxed">{{ product.description }}</p>

          <div class="space-y-3 mb-8">
            <div class="flex items-center justify-between py-2 border-b border-[var(--color-border)]">
              <span class="text-sm text-[var(--color-text-secondary)]">Unit</span>
              <span class="text-sm font-medium text-[var(--color-text)]">{{ product.unit?.name || 'N/A' }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-[var(--color-border)]">
              <span class="text-sm text-[var(--color-text-secondary)]">Availability</span>
              <span :class="product.status === 'active' ? 'text-green-600' : 'text-red-600'" class="text-sm font-medium">
                {{ product.status === 'active' ? 'In Stock' : 'Out of Stock' }}
              </span>
            </div>
          </div>

          <!-- Add to Cart -->
          <form @submit.prevent="addToCart" class="flex items-center gap-4">
            <div class="flex items-center border border-[var(--color-border)] rounded-full dark:border-gray-600">
              <button type="button" @click="quantity = Math.max(minQty, quantity - 1)" class="px-4 py-2 text-[var(--color-text)] hover:text-accent transition-colors">-</button>
              <input v-model.number="quantity" type="number" :min="minQty" :max="maxQty || undefined" class="w-16 text-center border-0 bg-transparent text-[var(--color-text)] font-medium focus:ring-0" />
              <button type="button" @click="quantity = maxQty ? Math.min(maxQty, quantity + 1) : quantity + 1" class="px-4 py-2 text-[var(--color-text)] hover:text-accent transition-colors">+</button>
            </div>
            <button type="submit" :disabled="adding" class="flex-1 bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors disabled:opacity-50">
              {{ adding ? 'Adding...' : 'Add to Cart' }}
            </button>
            <button v-if="$page.props.auth?.customer" type="button" @click="toggleWishlist" class="p-3 rounded-full border border-[var(--color-border)] hover:border-red-300 transition-colors dark:border-gray-600">
              <svg class="h-6 w-6 transition-colors" :class="isWishlisted ? 'text-red-500 fill-red-500' : 'text-gray-400 hover:text-red-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>
          </form>

          <div class="mt-3 text-xs text-[var(--color-text-secondary)]">
            <span>Min order: {{ minQty }} {{ product.unit?.short_name || 'pc' }}</span>
            <span class="mx-2">|</span>
            <span>Max order: {{ maxQty ? maxQty + ' ' + (product.unit?.short_name || 'pc') : 'No limit' }}</span>
          </div>

          <p v-if="message" class="mt-4 text-sm text-green-600 dark:text-green-400">{{ message }}</p>
          <p v-if="error" class="mt-4 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
        </div>
      </div>

      <!-- Variants -->
      <div v-if="product.variants?.length" class="mt-12">
        <h2 class="text-xl font-bold text-[var(--color-text)] mb-4">Available Variants</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="variant in product.variants" :key="variant.id" class="bg-white rounded-xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
            <h3 class="font-semibold text-[var(--color-text)]">{{ variant.name }}</h3>
            <p class="text-sm text-[var(--color-text-secondary)] mt-1">SKU: {{ variant.sku }}</p>
            <p class="text-accent font-bold mt-2">₦{{ Number(variant.selling_price || product.selling_price).toLocaleString() }}</p>
          </div>
        </div>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  product: Object,
  cartCount: { type: Number, default: 0 },
  wishlistIds: { type: Array, default: () => [] },
  company: Object,
})

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
