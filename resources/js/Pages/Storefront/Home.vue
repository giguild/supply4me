<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="home">
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-accent via-accent-light to-accent text-white">
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="text-center">
          <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-6 animate-fade-in">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            <span class="text-sm font-medium">{{ categories.length }} Categories Available</span>
          </div>

          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold mb-6 animate-slide-up">
            {{ company?.name || 'Welcome to Supply4Me' }}
          </h1>

          <p class="text-xl sm:text-2xl text-white/80 mb-8 max-w-2xl mx-auto animate-slide-up" style="animation-delay: 0.1s">
            Browse our products and place your order
          </p>

          <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up" style="animation-delay: 0.2s">
            <a href="/shop" class="bg-white text-accent px-8 py-3.5 rounded-full font-bold hover:bg-white/90 transition-all duration-300 hover:shadow-xl hover:scale-105">
              Shop Now
            </a>
            <a v-if="categories.length" href="#categories" class="border-2 border-white/50 text-white px-8 py-3.5 rounded-full font-bold hover:bg-white/10 transition-all duration-300">
              Browse Categories
            </a>
          </div>
        </div>
      </div>

      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
          <path d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,48C672,43,768,53,864,64C960,75,1056,85,1152,80C1248,75,1344,53,1392,42.7L1440,32V120H0Z" class="fill-[var(--color-bg)]"/>
        </svg>
      </div>
    </section>

    <!-- Categories Section -->
    <section v-if="categories.length" id="categories" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold text-[var(--color-text)]">Shop by Category</h2>
          <p class="text-[var(--color-text-secondary)] mt-1">Find exactly what you need</p>
        </div>
        <a href="/shop" class="text-sm font-semibold text-accent hover:text-accent-hover transition-colors">View All &rarr;</a>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        <a
          v-for="(cat, index) in categories.slice(0, 8)"
          :key="cat.id"
          :href="`/shop?category_id=${cat.id}`"
          class="group relative bg-white rounded-2xl border border-[var(--color-border)] p-6 text-center hover:shadow-lg hover:border-accent/50 transition-all duration-300 dark:bg-gray-800 dark:border-gray-700"
        >
          <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"
            :class="categoryColors[index % categoryColors.length]">
            <span class="text-xl font-bold text-[var(--color-text)] opacity-60">{{ cat.name.charAt(0).toUpperCase() }}</span>
          </div>
          <h3 class="font-semibold text-[var(--color-text)] group-hover:text-accent transition-colors text-sm sm:text-base">{{ cat.name }}</h3>
          <p class="text-xs text-[var(--color-text-secondary)] mt-1">{{ cat.products_count || 0 }} products</p>
        </a>
      </div>
    </section>

    <!-- Featured Products Carousel -->
    <section v-if="featured.length" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="flex items-center justify-between mb-8">
        <div>
          <h2 class="text-2xl sm:text-3xl font-bold text-[var(--color-text)]">Featured Products</h2>
          <p class="text-[var(--color-text-secondary)] mt-1">Handpicked just for you</p>
        </div>
        <a href="/shop" class="text-sm font-semibold text-accent hover:text-accent-hover transition-colors">View All &rarr;</a>
      </div>

      <div class="relative">
        <!-- Carousel track -->
        <div ref="carouselTrack" class="flex gap-6 overflow-x-auto snap-x snap-mandatory scrollbar-hide pb-4" @scroll="updateScrollState">
          <a
            v-for="product in featured"
            :key="product.id"
            :href="`/product/${product.slug}`"
            class="group flex-none w-64 sm:w-72 snap-start bg-white rounded-2xl border border-[var(--color-border)] overflow-hidden hover:shadow-xl transition-all duration-300 dark:bg-gray-800 dark:border-gray-700 hover:-translate-y-1"
          >
            <div class="relative aspect-square bg-gradient-to-br from-accent-50 to-white dark:from-gray-700 dark:to-gray-800 overflow-hidden">
              <img v-if="getProductImage(product)" :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="text-5xl font-bold text-accent/20">{{ product.name.charAt(0) }}</span>
              </div>
              <span v-if="product.category" class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-xs font-semibold px-3 py-1 rounded-full text-accent shadow-sm dark:bg-gray-800/90">
                {{ product.category.name }}
              </span>
            </div>
            <div class="p-4">
              <h3 class="font-semibold text-[var(--color-text)] group-hover:text-accent transition-colors line-clamp-2 text-sm">{{ product.name }}</h3>
              <p class="text-xs text-[var(--color-text-secondary)] mt-1">SKU: {{ product.sku }}</p>
              <div class="flex items-end justify-between mt-3 pt-3 border-t border-[var(--color-border)] dark:border-gray-700">
                <span class="text-lg font-extrabold text-accent">₦{{ Number(product.selling_price).toLocaleString() }}</span>
                <span class="text-xs font-medium text-accent bg-accent/10 px-3 py-1.5 rounded-full group-hover:bg-accent group-hover:text-white transition-colors duration-300">
                  View
                </span>
              </div>
            </div>
          </a>
        </div>

        <!-- Nav arrows -->
        <button v-if="canScrollLeft" @click="scrollCarousel(-1)"
          class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 w-10 h-10 bg-white dark:bg-gray-800 border border-[var(--color-border)] dark:border-gray-600 rounded-full shadow-lg flex items-center justify-center hover:bg-accent hover:text-white hover:border-accent transition-all duration-300 z-10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button v-if="canScrollRight" @click="scrollCarousel(1)"
          class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-3 w-10 h-10 bg-white dark:bg-gray-800 border border-[var(--color-border)] dark:border-gray-600 rounded-full shadow-lg flex items-center justify-center hover:bg-accent hover:text-white hover:border-accent transition-all duration-300 z-10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
    </section>

    <!-- Promo Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 p-8 sm:p-12 text-white">
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -top-20 -right-20 w-60 h-60 bg-white/10 rounded-full blur-2xl"></div>
          <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-white/10 rounded-full blur-2xl"></div>
        </div>
        <div class="relative flex flex-col sm:flex-row items-center justify-between gap-6">
          <div>
            <span class="inline-block bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full mb-3">LIMITED TIME</span>
            <h3 class="text-2xl sm:text-3xl font-extrabold mb-2">Bulk Orders Welcome</h3>
            <p class="text-white/90">Competitive pricing for large quantities. Contact us for custom quotes.</p>
          </div>
          <a href="/shop" class="shrink-0 bg-white text-orange-600 px-8 py-3.5 rounded-full font-bold hover:bg-white/90 transition-all duration-300 hover:shadow-xl hover:scale-105">
            View Products
          </a>
        </div>
      </div>
    </section>

    <!-- Newsletter / CTA -->
    <section class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-2 mb-6">
          <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
          <span class="text-sm font-medium">Stay Updated</span>
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Never Miss a Deal</h2>
        <p class="text-gray-400 text-lg mb-8 max-w-xl mx-auto">Get notified about new products, exclusive offers, and restocks.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
          <input type="email" placeholder="Enter your email" class="w-full sm:flex-1 px-5 py-3.5 rounded-full bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent" />
          <button class="w-full sm:w-auto bg-accent text-white px-8 py-3.5 rounded-full font-bold hover:bg-accent-hover transition-all duration-300 hover:shadow-lg hover:shadow-accent/25">
            Subscribe
          </button>
        </div>
      </div>
    </section>
  </StorefrontLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  categories: Array,
  brands: Array,
  featured: Array,
  cartCount: { type: Number, default: 0 },
  company: Object,
})

const carouselTrack = ref(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(true)

const categoryColors = [
  'bg-blue-100 dark:bg-blue-900/30',
  'bg-green-100 dark:bg-green-900/30',
  'bg-purple-100 dark:bg-purple-900/30',
  'bg-amber-100 dark:bg-amber-900/30',
  'bg-rose-100 dark:bg-rose-900/30',
  'bg-cyan-100 dark:bg-cyan-900/30',
  'bg-indigo-100 dark:bg-indigo-900/30',
  'bg-emerald-100 dark:bg-emerald-900/30',
]

function getProductImage(product) {
  const images = product.product_images
  return images?.length ? `/storage/${images[0]}` : null
}

function updateScrollState() {
  const el = carouselTrack.value
  if (!el) return
  canScrollLeft.value = el.scrollLeft > 10
  canScrollRight.value = el.scrollLeft < el.scrollWidth - el.clientWidth - 10
}

function scrollCarousel(direction) {
  const el = carouselTrack.value
  if (!el) return
  const cardWidth = el.firstElementChild?.offsetWidth || 288
  el.scrollBy({ left: direction * (cardWidth + 24), behavior: 'smooth' })
}

onMounted(() => updateScrollState())
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slide-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out forwards;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out forwards;
  opacity: 0;
}

.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>