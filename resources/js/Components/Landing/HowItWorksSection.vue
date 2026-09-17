<template>
  <section class="py-24 bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#9F5124]/5 rounded-full blur-3xl"></div>
      <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-[#9F5124]/8 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-16">
        <div>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4">How It Works</h2>
          <p class="text-xl text-white/60">Simple. Fast. Reliable.</p>
        </div>
        <div class="mt-6 lg:mt-0">
          <p class="text-[#9F5124] font-caveat text-2xl italic">From Order to Opportunity.</p>
        </div>
      </div>

      <!-- Steps -->
      <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 relative">
        <!-- Connection Line (Desktop) -->
        <div class="hidden lg:block absolute top-16 left-[12.5%] right-[12.5%] h-0.5 bg-gradient-to-r from-[#9F5124]/0 via-[#9F5124]/50 to-[#9F5124]/0"></div>

        <div 
          v-for="(step, index) in steps" 
          :key="step.number"
          class="relative"
        >
          <!-- Step Circle -->
          <div class="relative z-10 w-32 h-32 mx-auto mb-6">
            <div class="absolute inset-0 rounded-full bg-[#9F5124]/20 animate-pulse" :style="{ animationDelay: (index * 0.2) + 's' }"></div>
            <div class="absolute inset-2 rounded-full bg-[#111A20] border-2 border-[#9F5124]/50 flex items-center justify-center">
              <div class="w-16 h-16 rounded-full bg-[#9F5124]/10 flex items-center justify-center">
                <svg class="w-8 h-8 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="step.icon"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Content -->
          <div class="text-center px-4">
            <div class="text-sm font-bold text-[#9F5124] mb-2">{{ step.number }}</div>
            <h3 class="text-xl font-bold text-white mb-3">{{ step.title }}</h3>
            <p class="text-white/60 text-sm leading-relaxed">{{ step.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const steps = [
  {
    number: '01',
    title: 'Browse & Order',
    description: 'Find the products you need and place your order.',
    icon: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z'
  },
  {
    number: '02',
    title: 'Make Payment',
    description: 'Pay securely and receive instant confirmation.',
    icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
  },
  {
    number: '03',
    title: 'We Prepare',
    description: 'Our team picks and packs your order.',
    icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
  },
  {
    number: '04',
    title: 'Fast Delivery',
    description: 'Receive your products on time.',
    icon: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'
  },
]
</script>

<style scoped>
.font-caveat {
  font-family: 'Caveat', cursive;
}
</style>
