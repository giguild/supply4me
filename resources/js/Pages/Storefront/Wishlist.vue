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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-extrabold text-white">My Wishlist</h1>
              <p class="text-white/60 text-sm">{{ items.length }} {{ items.length === 1 ? 'item' : 'items' }} saved</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

          <!-- Empty State -->
          <div v-if="items.length === 0" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-full bg-[#9F5124]/10 flex items-center justify-center mb-4">
              <svg class="w-8 h-8 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </div>
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-2">Your wishlist is empty</h2>
            <p class="text-[#616262] text-sm mb-6">Save products you love for later.</p>
            <a href="/shop" class="inline-block bg-[#9F5124] text-white px-8 py-3 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25">
              Browse Products
            </a>
          </div>

          <!-- Wishlist Grid -->
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="item in items" :key="item.id" class="bg-white rounded-2xl border border-[#2D2C2C]/5 overflow-hidden hover:shadow-lg hover:shadow-[#2D2C2C]/5 transition-all duration-300 group">
              <a :href="`/product/${item.product?.slug}`" class="block">
                <div class="aspect-square bg-[#F1EFEE] flex items-center justify-center relative overflow-hidden">
                  <span class="text-5xl font-extrabold text-[#9F5124]/15 group-hover:scale-110 transition-transform duration-500">{{ item.product?.name?.charAt(0) || '?' }}</span>
                </div>
                <div class="p-4">
                  <h3 class="font-bold text-[#2D2C2C] line-clamp-2">{{ item.product?.name }}</h3>
                  <p class="text-xs text-[#616262] mt-1">{{ item.product?.sku }}</p>
                  <div class="flex items-center gap-2 mt-2">
                    <span v-if="item.product?.category" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">{{ item.product.category.name }}</span>
                    <span v-if="item.product?.brand" class="px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-[#616262]">{{ item.product.brand.name }}</span>
                  </div>
                  <p class="text-lg font-extrabold text-[#9F5124] mt-3">₦{{ Number(item.product?.selling_price || 0).toLocaleString() }}</p>
                </div>
              </a>
              <div class="px-4 pb-4 flex gap-2">
                <form @submit.prevent="addToCart(item.product)" class="flex-1">
                  <button type="submit" class="w-full bg-[#9F5124] text-white py-2.5 rounded-full text-sm font-bold hover:bg-[#8a4620] transition-all duration-300">
                    Add to Cart
                  </button>
                </form>
                <form @submit.prevent="removeItem(item.id)">
                  <button type="submit" class="p-2.5 rounded-full border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </form>
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
import { router } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

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
