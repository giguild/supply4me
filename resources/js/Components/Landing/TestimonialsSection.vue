<template>
  <section class="py-24 bg-[#F1EFEE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-12">
        <div>
          <h2 class="text-3xl sm:text-4xl font-extrabold text-[#2D2C2C] mb-4">What Our Partners Say</h2>
          <p class="text-lg text-[#616262]">Real businesses. Real growth.</p>
        </div>
        <a href="#" class="mt-4 sm:mt-0 text-[#9F5124] font-semibold hover:text-[#8a4620] transition-colors flex items-center gap-2">
          More Testimonials
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
          </svg>
        </a>
      </div>

      <!-- Testimonials Grid -->
      <div class="grid md:grid-cols-3 gap-8">
        <div 
          v-for="(testimonial, index) in displayTestimonials" 
          :key="testimonial.name"
          class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 border border-[#2D2C2C]/5"
        >
          <!-- Quote Icon -->
          <svg class="w-10 h-10 text-[#9F5124] mb-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
          </svg>

          <!-- Quote -->
          <p class="text-[#2D2C2C] text-lg leading-relaxed mb-8 italic">
            "{{ testimonial.quote }}"
          </p>

          <!-- Author -->
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full overflow-hidden bg-gradient-to-br from-[#9F5124] to-[#7a3d1a] flex items-center justify-center">
              <span class="text-white font-bold text-lg">{{ testimonial.name.charAt(0) }}</span>
            </div>
            <div>
              <p class="font-bold text-[#2D2C2C]">{{ testimonial.name }}</p>
              <p class="text-sm text-[#616262]">{{ testimonial.role }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  testimonials: { type: Array, default: () => [] }
})

const defaultTestimonials = [
  {
    name: 'Chinedu A.',
    role: 'Retail Store Owner, Lagos',
    quote: 'Supply 4 Me has made it easier for us to stock genuine products at great prices. Delivery is always on time.'
  },
  {
    name: 'Fatima R.',
    role: 'Mini Mart, Kano',
    quote: 'A reliable partner for our business. The variety and pricing help us serve our customers better.'
  },
  {
    name: 'Tunde M.',
    role: 'Distributor, Abuja',
    quote: 'Professional service and genuine products. Highly recommended for any growing business.'
  },
]

import { computed } from 'vue'

const displayTestimonials = computed(() => {
  if (props.testimonials && props.testimonials.length > 0) {
    return props.testimonials.slice(0, 3)
  }
  return defaultTestimonials
})
</script>
