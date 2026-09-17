<template>
  <section class="py-16 bg-gradient-to-r from-[#2D2C2C] via-[#1a1a1a] to-[#2D2C2C] relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10 metrics-grid-pattern"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
        <div 
          v-for="(metric, index) in metrics" 
          :key="metric.label"
          class="text-center"
        >
          <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-[#9F5124]/20 flex items-center justify-center">
            <svg class="w-7 h-7 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="metric.icon"/>
            </svg>
          </div>
          <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1">{{ metric.value }}</div>
          <div class="text-sm text-white/60 font-medium">{{ metric.label }}</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  stats: { type: Object, default: () => ({}) }
})

const defaultMetrics = [
  { value: '10,000+', label: 'Retailers Supplied', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
  { value: '500+', label: 'Product Categories', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
  { value: '36', label: 'States Covered', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { value: '99%', label: 'On-Time Delivery', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
]

import { computed } from 'vue'

const metrics = computed(() => {
  if (props.stats && Object.keys(props.stats).length > 0) {
    return defaultMetrics.map((m, i) => ({
      ...m,
      value: Object.values(props.stats)[i] || m.value
    }))
  }
  return defaultMetrics
})
</script>
