<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="account">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-2xl font-bold text-[var(--color-text)] mb-6">My Wishlist</h1>

      <div v-if="items.length === 0" class="text-center py-16 bg-white rounded-2xl border border-[var(--color-border)] dark:bg-gray-800 dark:border-gray-700">
        <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
        <h2 class="text-xl font-semibold text-[var(--color-text)] mb-2">Your wishlist is empty</h2>
        <p class="text-[var(--color-text-secondary)] mb-6">Save products you love for later.</p>
        <a href="/" class="inline-block bg-accent text-white px-6 py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors">Browse Products</a>
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="item in items" :key="item.id" class="bg-white rounded-2xl border border-[var(--color-border)] overflow-hidden dark:bg-gray-800 dark:border-gray-700">
          <a :href="`/product/${item.product?.slug}`" class="block">
            <div class="aspect-square bg-gradient-to-br from-accent-50 to-white flex items-center justify-center dark:from-gray-700 dark:to-gray-800">
              <span class="text-5xl font-bold text-accent/30">{{ item.product?.name?.charAt(0) || '?' }}</span>
            </div>
            <div class="p-4">
              <h3 class="font-semibold text-[var(--color-text)] line-clamp-2">{{ item.product?.name }}</h3>
              <p class="text-xs text-[var(--color-text-secondary)] mt-1">{{ item.product?.sku }}</p>
              <div class="flex items-center gap-2 mt-2">
                <span v-if="item.product?.category" class="badge badge-purple text-xs">{{ item.product.category.name }}</span>
                <span v-if="item.product?.brand" class="badge badge-gray text-xs">{{ item.product.brand.name }}</span>
              </div>
              <p class="text-lg font-bold text-accent mt-3">₦{{ Number(item.product?.selling_price || 0).toLocaleString() }}</p>
            </div>
          </a>
          <div class="px-4 pb-4 flex gap-2">
            <form @submit.prevent="addToCart(item.product)" class="flex-1">
              <button type="submit" class="w-full bg-accent text-white py-2 rounded-full text-sm font-semibold hover:bg-accent-hover transition-colors">Add to Cart</button>
            </form>
            <form @submit.prevent="removeItem(item.id)">
              <button type="submit" class="p-2 rounded-full border border-[var(--color-border)] text-red-500 hover:bg-red-50 transition-colors dark:border-gray-600 dark:hover:bg-red-900/30">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  items: Array,
  cartCount: { type: Number, default: 0 },
})

function addToCart(product) {
  router.post('/cart/add', {
    product_id: product.id,
    quantity: product.minimum_order_quantity || 1,
  })
}

function removeItem(id) {
  router.delete(`/wishlist/${id}`)
}
</script>
