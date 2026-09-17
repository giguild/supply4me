<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <!-- Hero Banner -->
      <section class="relative bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute top-1/2 left-1/4 w-96 h-96 bg-[#9F5124]/8 rounded-full blur-3xl"></div>
          <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-[#9F5124]/5 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <div class="inline-flex items-center gap-2 bg-[#9F5124]/10 border border-[#9F5124]/20 rounded-full px-4 py-2 mb-5">
              <svg class="w-4 h-4 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
              <span class="text-sm font-medium text-[#9F5124]">FMCG CATALOGUE</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4">
              Shop Our <span class="text-[#9F5124]">Products</span>
            </h1>
            <p class="text-lg text-white/60 max-w-xl mx-auto">
              Quality FMCG products at competitive wholesale prices for your business.
            </p>
          </div>
        </div>

        <!-- Bottom wave -->
        <div class="absolute bottom-0 left-0 right-0">
          <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
            <path d="M0,40L60,36C120,32,240,24,360,28C480,32,600,48,720,52C840,56,960,48,1080,40C1200,32,1320,24,1380,20L1440,16V60H0Z" class="fill-[#F1EFEE]"/>
          </svg>
        </div>
      </section>

      <!-- Category Pills -->
      <section class="bg-[#F1EFEE] py-6 border-b border-[#2D2C2C]/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center gap-3 overflow-x-auto scrollbar-hide pb-1">
            <button
              @click="setCategory('')"
              class="shrink-0 px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-300"
              :class="!localFilters.category_id
                ? 'bg-[#9F5124] text-white shadow-md shadow-[#9F5124]/25'
                : 'bg-white text-[#2D2C2C] border border-[#2D2C2C]/10 hover:border-[#9F5124]/40 hover:text-[#9F5124]'"
            >
              All Products
            </button>
            <button
              v-for="cat in categories"
              :key="cat.id"
              @click="setCategory(cat.id)"
              class="shrink-0 px-5 py-2.5 rounded-full text-sm font-semibold transition-all duration-300"
              :class="localFilters.category_id === cat.id
                ? 'bg-[#9F5124] text-white shadow-md shadow-[#9F5124]/25'
                : 'bg-white text-[#2D2C2C] border border-[#2D2C2C]/10 hover:border-[#9F5124]/40 hover:text-[#9F5124]'"
            >
              {{ cat.name }}
            </button>
          </div>
        </div>
      </section>

      <!-- Main Content -->
      <section class="bg-[#F1EFEE] py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex flex-col lg:flex-row gap-8">

            <!-- Sidebar Filters (Desktop) -->
            <aside class="hidden lg:block w-64 shrink-0">
              <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 sticky top-24">
                <h3 class="font-bold text-[#2D2C2C] mb-5 flex items-center gap-2">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                  </svg>
                  Filters
                </h3>

                <!-- Search -->
                <div class="mb-5">
                  <label class="form-label text-[#2D2C2C]">Search</label>
                  <div class="relative">
                    <input
                      v-model="localFilters.search"
                      @keyup.enter="applyFilters"
                      type="text"
                      placeholder="Search products..."
                      class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-sm text-[#2D2C2C] placeholder-[#616262] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all"
                    />
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-[#616262]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                  </div>
                </div>

                <!-- Category -->
                <div class="mb-5">
                  <label class="form-label text-[#2D2C2C]">Category</label>
                  <select
                    v-model="localFilters.category_id"
                    @change="applyFilters"
                    class="w-full px-4 py-2.5 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-sm text-[#2D2C2C] focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all"
                  >
                    <option value="">All Categories</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                </div>

                <!-- Brand -->
                <div class="mb-5">
                  <label class="form-label text-[#2D2C2C]">Brand</label>
                  <select
                    v-model="localFilters.brand_id"
                    @change="applyFilters"
                    class="w-full px-4 py-2.5 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-sm text-[#2D2C2C] focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all"
                  >
                    <option value="">All Brands</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                  </select>
                </div>

                <!-- Sort -->
                <div>
                  <label class="form-label text-[#2D2C2C]">Sort By</label>
                  <select
                    v-model="localFilters.sort"
                    @change="applyFilters"
                    class="w-full px-4 py-2.5 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-sm text-[#2D2C2C] focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all"
                  >
                    <option value="">Newest</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                  </select>
                </div>
              </div>
            </aside>

            <!-- Products Area -->
            <div class="flex-1">
              <!-- Mobile Filters Bar -->
              <div class="lg:hidden flex items-center gap-3 mb-6 overflow-x-auto scrollbar-hide">
                <div class="relative flex-1 min-w-0">
                  <input
                    v-model="localFilters.search"
                    @keyup.enter="applyFilters"
                    type="text"
                    placeholder="Search products..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-[#2D2C2C]/10 bg-white text-sm text-[#2D2C2C] placeholder-[#616262] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124]"
                  />
                  <svg class="absolute left-3 top-2.5 h-4 w-4 text-[#616262]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
                <select
                  v-model="localFilters.sort"
                  @change="applyFilters"
                  class="shrink-0 px-4 py-2.5 rounded-xl border border-[#2D2C2C]/10 bg-white text-sm text-[#2D2C2C] focus:ring-2 focus:ring-[#9F5124]/30"
                >
                  <option value="">Newest</option>
                  <option value="price_asc">Price: Low to High</option>
                  <option value="price_desc">Price: High to Low</option>
                </select>
              </div>

              <!-- Results Count -->
              <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-[#616262]">
                  Showing <span class="font-semibold text-[#2D2C2C]">{{ products.data?.length || 0 }}</span> of <span class="font-semibold text-[#2D2C2C]">{{ products.total || 0 }}</span> products
                </p>
                <div v-if="hasActiveFilters" class="flex items-center gap-2">
                  <button
                    @click="clearFilters"
                    class="text-sm text-[#9F5124] hover:text-[#8a4620] font-medium flex items-center gap-1 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear filters
                  </button>
                </div>
              </div>

              <!-- Products Grid -->
              <div v-if="products.data?.length" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <a
                  v-for="product in products.data"
                  :key="product.id"
                  :href="`/product/${product.slug}`"
                  class="group bg-white rounded-2xl border border-[#2D2C2C]/5 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                >
                  <!-- Image -->
                  <div class="relative aspect-square bg-gradient-to-br from-[#F1EFEE] to-white overflow-hidden">
                    <img
                      v-if="getProductImage(product)"
                      :src="getProductImage(product)"
                      :alt="product.name"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      loading="lazy"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center">
                      <span class="text-5xl font-bold text-[#9F5124]/15">{{ product.name.charAt(0) }}</span>
                    </div>

                    <!-- Wishlist -->
                    <button
                      v-if="$page.props.auth?.customer"
                      @click.prevent="toggleWishlist(product.id)"
                      class="absolute top-3 right-3 w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full shadow-md flex items-center justify-center hover:bg-white hover:shadow-lg transition-all duration-300"
                    >
                      <svg
                        class="h-4 w-4 transition-colors"
                        :class="wishlistIds.includes(product.id) ? 'text-red-500 fill-red-500' : 'text-[#616262] hover:text-red-400'"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                      </svg>
                    </button>

                    <!-- Category Badge -->
                    <span
                      v-if="product.category"
                      class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-xs font-semibold px-3 py-1 rounded-full text-[#9F5124] shadow-sm"
                    >
                      {{ product.category.name }}
                    </span>
                  </div>

                  <!-- Content -->
                  <div class="p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-2 mb-2">
                      <h3 class="font-semibold text-[#2D2C2C] group-hover:text-[#9F5124] transition-colors line-clamp-2 flex-1 text-sm sm:text-base">
                        {{ product.name }}
                      </h3>
                    </div>

                    <p class="text-xs text-[#616262] mb-2">SKU: {{ product.sku }}</p>

                    <div v-if="product.brand" class="mb-3">
                      <span class="inline-block text-xs font-medium text-[#616262] bg-[#F1EFEE] px-2.5 py-1 rounded-md">
                        {{ product.brand.name }}
                      </span>
                    </div>

                    <div class="flex items-end justify-between pt-3 border-t border-[#2D2C2C]/5">
                      <div>
                        <span class="text-lg sm:text-xl font-extrabold text-[#9F5124]">
                          ₦{{ Number(product.selling_price).toLocaleString() }}
                        </span>
                        <p class="text-xs text-[#616262] mt-0.5">
                          Min: {{ product.minimum_order_quantity || 1 }} {{ product.unit?.short_name || 'pc' }}
                        </p>
                      </div>
                      <span class="text-xs font-semibold text-[#9F5124] bg-[#9F5124]/10 px-3 py-1.5 rounded-full group-hover:bg-[#9F5124] group-hover:text-white transition-colors duration-300">
                        View
                      </span>
                    </div>
                  </div>
                </a>
              </div>

              <!-- Empty State -->
              <div v-else class="text-center py-20 bg-white rounded-3xl border border-[#2D2C2C]/5">
                <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-[#9F5124]/10 flex items-center justify-center">
                  <svg class="w-10 h-10 text-[#9F5124]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                  </svg>
                </div>
                <h3 class="text-xl font-bold text-[#2D2C2C] mb-2">No products found</h3>
                <p class="text-[#616262] mb-6">Try adjusting your filters or search terms</p>
                <button
                  @click="clearFilters"
                  class="inline-flex items-center gap-2 bg-[#9F5124] text-white px-6 py-3 rounded-full font-semibold hover:bg-[#8a4620] transition-all duration-300"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  Reset Filters
                </button>
              </div>

              <!-- Pagination -->
              <div v-if="products.last_page > 1" class="flex items-center justify-center gap-2 mt-10">
                <a
                  :href="`?page=${Math.max(1, products.current_page - 1)}`"
                  class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-medium transition-all duration-300 border"
                  :class="products.current_page === 1
                    ? 'bg-white border-[#2D2C2C]/5 text-[#616262]/40 pointer-events-none'
                    : 'bg-white border-[#2D2C2C]/10 text-[#2D2C2C] hover:border-[#9F5124]/40 hover:text-[#9F5124]'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                  </svg>
                </a>

                <a
                  v-for="page in visiblePages"
                  :key="page"
                  :href="`?page=${page}`"
                  class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-medium transition-all duration-300 border"
                  :class="page === products.current_page
                    ? 'bg-[#9F5124] text-white border-[#9F5124] shadow-md shadow-[#9F5124]/25'
                    : 'bg-white border-[#2D2C2C]/10 text-[#2D2C2C] hover:border-[#9F5124]/40 hover:text-[#9F5124]'"
                >
                  {{ page }}
                </a>

                <a
                  :href="`?page=${Math.min(products.last_page, products.current_page + 1)}`"
                  class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-medium transition-all duration-300 border"
                  :class="products.current_page === products.last_page
                    ? 'bg-white border-[#2D2C2C]/5 text-[#616262]/40 pointer-events-none'
                    : 'bg-white border-[#2D2C2C]/10 text-[#2D2C2C] hover:border-[#9F5124]/40 hover:text-[#9F5124]'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </a>
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
import { reactive, ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

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

const hasActiveFilters = computed(() => {
  return localFilters.search || localFilters.category_id || localFilters.brand_id || localFilters.sort
})

const visiblePages = computed(() => {
  const total = props.products.last_page
  const current = props.products.current_page
  const pages = []

  let start = Math.max(1, current - 2)
  let end = Math.min(total, current + 2)

  if (end - start < 4) {
    if (start === 1) {
      end = Math.min(total, start + 4)
    } else {
      start = Math.max(1, end - 4)
    }
  }

  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

function setCategory(categoryId) {
  localFilters.category_id = categoryId
  applyFilters()
}

function clearFilters() {
  localFilters.search = ''
  localFilters.category_id = ''
  localFilters.brand_id = ''
  localFilters.sort = ''
  applyFilters()
}

function applyFilters() {
  const clean = {}
  if (localFilters.search) clean.search = localFilters.search
  if (localFilters.category_id) clean.category_id = localFilters.category_id
  if (localFilters.brand_id) clean.brand_id = localFilters.brand_id
  if (localFilters.sort) clean.sort = localFilters.sort
  router.get('/shop', clean, { preserveState: true, replace: true })
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

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
