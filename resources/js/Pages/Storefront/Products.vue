<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="products" searchable>
    <template #search>
      <form @submit.prevent="applyFilters" class="relative">
        <input
          v-model="localFilters.search"
          type="text"
          placeholder="Search products..."
          class="w-full pl-10 pr-4 py-2 rounded-full border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white"
        />
        <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      </form>
    </template>

    <!-- Page Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-4">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-3xl sm:text-4xl font-bold text-[var(--color-text)]">All Products</h1>
          <p class="text-[var(--color-text-secondary)] mt-1">{{ products.total || 0 }} products found</p>
        </div>
        <div class="flex flex-wrap gap-3">
          <select v-model="localFilters.category_id" @change="applyFilters" class="px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
          </select>
          <select v-model="localFilters.brand_id" @change="applyFilters" class="px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <option value="">All Brands</option>
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
          </select>
          <select v-model="localFilters.sort" @change="applyFilters" class="px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white">
            <option value="">Newest</option>
            <option value="price_asc">Price: Low to High</option>
            <option value="price_desc">Price: High to Low</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
      <div v-if="products.data?.length" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        <a
          v-for="product in products.data"
          :key="product.id"
          :href="`/product/${product.slug}`"
          class="group bg-white rounded-2xl border border-[var(--color-border)] overflow-hidden hover:shadow-xl transition-all duration-300 dark:bg-gray-800 dark:border-gray-700 hover:-translate-y-1"
        >
          <div class="relative aspect-square bg-gradient-to-br from-accent-50 to-white dark:from-gray-700 dark:to-gray-800 overflow-hidden">
            <img v-if="getProductImage(product)" :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
            <div v-else class="w-full h-full flex items-center justify-center">
              <span class="text-6xl font-bold text-accent/20">{{ product.name.charAt(0) }}</span>
            </div>

            <button
              v-if="$page.props.auth?.customer"
              @click.prevent="toggleWishlist(product.id)"
              class="absolute top-3 right-3 p-2.5 bg-white/90 backdrop-blur-sm rounded-full shadow-md hover:bg-white hover:shadow-lg transition-all duration-300 dark:bg-gray-800/90 dark:hover:bg-gray-800"
            >
              <svg class="h-4 w-4 transition-colors" :class="wishlistIds.includes(product.id) ? 'text-red-500 fill-red-500' : 'text-gray-400 hover:text-red-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </button>

            <span v-if="product.category" class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-xs font-semibold px-3 py-1 rounded-full text-accent shadow-sm dark:bg-gray-800/90 dark:text-accent">
              {{ product.category.name }}
            </span>
          </div>

          <div class="p-5">
            <div class="flex items-start justify-between gap-2 mb-2">
              <h3 class="font-semibold text-[var(--color-text)] group-hover:text-accent transition-colors line-clamp-2 flex-1">{{ product.name }}</h3>
            </div>

            <p class="text-xs text-[var(--color-text-secondary)] mb-3">SKU: {{ product.sku }}</p>

            <div class="flex items-center gap-2 mb-4">
              <span v-if="product.brand" class="badge badge-gray text-xs">{{ product.brand.name }}</span>
            </div>

            <div class="flex items-end justify-between pt-3 border-t border-[var(--color-border)] dark:border-gray-700">
              <div>
                <span class="text-xl font-extrabold text-accent">₦{{ Number(product.selling_price).toLocaleString() }}</span>
                <p class="text-xs text-[var(--color-text-secondary)] mt-0.5">Min: {{ product.minimum_order_quantity || 1 }} {{ product.unit?.short_name || 'pc' }}</p>
              </div>
              <span class="text-xs font-medium text-accent bg-accent/10 px-3 py-1.5 rounded-full group-hover:bg-accent group-hover:text-white transition-colors duration-300">
                View
              </span>
            </div>
          </div>
        </a>
      </div>

      <div v-else class="text-center py-20 bg-white rounded-3xl border border-[var(--color-border)] dark:bg-gray-800 dark:border-gray-700">
        <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
          <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        </div>
        <h3 class="text-xl font-semibold text-[var(--color-text)] mb-2">No products found</h3>
        <p class="text-[var(--color-text-secondary)]">Try adjusting your filters or search terms</p>
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="flex justify-center gap-2 mt-10">
        <a
          v-for="page in products.last_page"
          :key="page"
          :href="`?page=${page}`"
          class="px-5 py-2.5 rounded-xl text-sm font-medium transition-all duration-300"
          :class="page === products.current_page ? 'bg-accent text-white shadow-lg shadow-accent/25' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent hover:text-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'"
        >
          {{ page }}
        </a>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  products: Object,
  categories: Array,
  brands: Array,
  filters: Object,
  cartCount: { type: Number, default: 0 },
  wishlistIds: { type: Array, default: () => [] },
  company: Object,
})

const wishlistIds = ref([...props.wishlistIds])

const localFilters = reactive({
  search: props.filters?.search || '',
  category_id: props.filters?.category_id || '',
  brand_id: props.filters?.brand_id || '',
  sort: props.filters?.sort || '',
})

function applyFilters() {
  router.get('/shop', localFilters, { preserveState: true, replace: true })
}

function toggleWishlist(productId) {
  fetch('/wishlist/toggle', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || ''),
    },
    body: JSON.stringify({ product_id: productId }),
  })
    .then(res => res.json())
    .then(data => {
      if (data.added) {
        wishlistIds.value.push(productId)
      } else {
        wishlistIds.value = wishlistIds.value.filter(id => id !== productId)
      }
    })
}

function getProductImage(product) {
  const images = product.product_images
  return images?.length ? `/storage/${images[0]}` : null
}
</script>