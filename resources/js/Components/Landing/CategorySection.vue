<template>
  <section class="py-20 bg-[#F1EFEE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <h2 class="text-3xl sm:text-4xl font-extrabold text-[#2D2C2C] mb-4">Shop by Category</h2>
        <p class="text-lg text-[#616262] max-w-2xl mx-auto">Everything your business needs, all in one place.</p>
      </div>

      <!-- Category Grid -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-6">
        <a
          v-for="(category, index) in displayCategories"
          :key="category.id || index"
          :href="category.href"
          class="group relative bg-white rounded-2xl p-6 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-[#2D2C2C]/5"
        >
          <!-- Icon/Image -->
          <div class="w-16 h-16 mx-auto mb-4 rounded-2xl flex items-center justify-center transition-transform duration-300 group-hover:scale-110"
            :class="category.bgClass">
            <span class="text-3xl">{{ category.emoji }}</span>
          </div>
          
          <!-- Content -->
          <h3 class="font-bold text-[#2D2C2C] group-hover:text-[#9F5124] transition-colors mb-1">{{ category.name }}</h3>
          <p class="text-sm text-[#616262] mb-3">{{ category.description }}</p>
          
          <!-- Arrow -->
          <div class="w-8 h-8 mx-auto rounded-full bg-[#9F5124]/10 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
            <svg class="w-4 h-4 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </a>
      </div>

      <!-- CTA -->
      <div class="text-center mt-10">
        <a 
          href="/shop" 
          class="inline-flex items-center gap-2 bg-[#9F5124] text-white px-8 py-3.5 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25"
        >
          View All Categories
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  categories: { type: Array, default: () => [] }
})

const defaultCategories = [
  { name: 'Beverages', emoji: '🥤', description: 'Drinks & refreshments', bgClass: 'bg-blue-50', href: '/shop?category=Beverages' },
  { name: 'Food Items', emoji: '🍽️', description: 'Essential food products', bgClass: 'bg-amber-50', href: '/shop?category=Food' },
  { name: 'Personal Care', emoji: '🧴', description: 'Hygiene & beauty', bgClass: 'bg-purple-50', href: '/shop?category=Personal+Care' },
  { name: 'Household', emoji: '🏠', description: 'Home essentials', bgClass: 'bg-green-50', href: '/shop?category=Household' },
  { name: 'Baby & Kids', emoji: '👶', description: 'Family care products', bgClass: 'bg-pink-50', href: '/shop?category=Baby' },
  { name: 'And More', emoji: '📦', description: 'Explore all products', bgClass: 'bg-gray-50', href: '/shop' },
]

const displayCategories = computed(() => {
  if (props.categories && props.categories.length > 0) {
    return props.categories.slice(0, 6).map((cat, i) => ({
      id: cat.id,
      name: cat.name,
      emoji: defaultCategories[i]?.emoji || '📦',
      description: `${cat.products_count || 0} products`,
      bgClass: defaultCategories[i]?.bgClass || 'bg-gray-50',
      href: `/shop?category_id=${cat.id}`
    }))
  }
  return defaultCategories
})
</script>
