<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="home" searchable>
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

    <!-- Hero -->
    <section class="bg-gradient-to-br from-accent to-accent-light text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-bold mb-4">{{ company?.name || 'Welcome' }}</h1>
        <p class="text-xl text-white/80">Browse our products and place your order</p>
      </div>
    </section>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex flex-wrap gap-4 items-center">
        <select v-model="localFilters.category_id" @change="applyFilters" class="px-4 py-2 rounded-full border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <select v-model="localFilters.brand_id" @change="applyFilters" class="px-4 py-2 rounded-full border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white">
          <option value="">All Brands</option>
          <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
        </select>
        <select v-model="localFilters.sort" @change="applyFilters" class="px-4 py-2 rounded-full border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white">
          <option value="">Newest</option>
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
        </select>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
      <div v-if="products.data?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <a
          v-for="product in products.data"
          :key="product.id"
          :href="`/product/${product.slug}`"
          class="group bg-white rounded-2xl border border-[var(--color-border)] overflow-hidden hover:shadow-lg transition-all duration-300 dark:bg-gray-800 dark:border-gray-700"
        >
          <div class="aspect-square bg-gradient-to-br from-accent-50 to-white flex items-center justify-center dark:from-gray-700 dark:to-gray-800 overflow-hidden">
            <img v-if="getProductImage(product)" :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-cover" />
            <span v-else class="text-5xl font-bold text-accent/30">{{ product.name.charAt(0) }}</span>
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-[var(--color-text)] group-hover:text-accent transition-colors line-clamp-2">{{ product.name }}</h3>
            <p class="text-xs text-[var(--color-text-secondary)] mt-1">{{ product.sku }}</p>
            <div class="flex items-center gap-2 mt-2">
              <span v-if="product.category" class="badge badge-purple text-xs">{{ product.category.name }}</span>
              <span v-if="product.brand" class="badge badge-gray text-xs">{{ product.brand.name }}</span>
            </div>
            <div class="flex items-center justify-between mt-4">
              <span class="text-lg font-bold text-accent">₦{{ Number(product.selling_price).toLocaleString() }}</span>
              <div class="flex items-center gap-2">
                <span class="text-xs text-[var(--color-text-secondary)]">Min: {{ product.minimum_order_quantity || 1 }} {{ product.unit?.short_name || 'pc' }}</span>
                <button v-if="$page.props.auth?.customer" @click.prevent="toggleWishlist(product.id)" class="p-1 rounded-full hover:bg-red-50 transition-colors dark:hover:bg-red-900/30">
                  <svg class="h-5 w-5 transition-colors" :class="wishlistIds.includes(product.id) ? 'text-red-500 fill-red-500' : 'text-gray-400 hover:text-red-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </button>
              </div>
            </div>
          </div>
        </a>
      </div>

      <div v-else class="text-center py-16">
        <p class="text-[var(--color-text-secondary)] text-lg">No products found</p>
      </div>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="flex justify-center gap-2 mt-8">
        <a
          v-for="page in products.last_page"
          :key="page"
          :href="`?page=${page}`"
          class="px-4 py-2 rounded-full text-sm font-medium transition-colors"
          :class="page === products.current_page ? 'bg-accent text-white' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'"
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
  router.get('/', localFilters, { preserveState: true, replace: true })
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
  const images = product.media?.filter(m => m.collection_name === 'images')
  return images?.length ? images[0].original_url : null
}
</script>
