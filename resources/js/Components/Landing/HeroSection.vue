<template>
  <section class="relative min-h-screen bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-1/4 -left-32 w-96 h-96 bg-[#9F5124]/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-[#9F5124]/8 rounded-full blur-3xl"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#9F5124]/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Grid Pattern -->
    <div class="absolute inset-0 hero-grid-pattern opacity-[0.03]"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 lg:pt-40 pb-20">
      <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <!-- Left Content -->
        <div class="text-center lg:text-left">
          <!-- Eyebrow -->
          <div class="inline-flex items-center gap-2 bg-[#9F5124]/10 border border-[#9F5124]/20 rounded-full px-4 py-2 mb-6 animate-fade-in">
            <span class="w-2 h-2 bg-[#9F5124] rounded-full animate-pulse"></span>
            <span class="text-sm font-medium text-[#9F5124]">TRUSTED FMCG SUPPLY PARTNER</span>
          </div>

          <!-- Headline -->
          <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white mb-6 leading-tight animate-fade-in stagger-1">
            Everyday Supplies.
            <span class="block text-[#9F5124]">Greater Opportunities.</span>
          </h1>

          <!-- Supporting Copy -->
          <p class="text-lg sm:text-xl text-white/70 mb-8 max-w-xl mx-auto lg:mx-0 animate-fade-in stagger-2">
            Supply 4 Me connects retailers and businesses with genuine FMCG products at competitive prices — powered by a reliable distribution network across Nigeria.
          </p>

          <!-- CTAs -->
          <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start animate-fade-in stagger-3">
            <a 
              href="/store-login" 
              class="w-full sm:w-auto bg-[#9F5124] text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-[#8a4620] transition-all duration-300 hover:shadow-xl hover:shadow-[#9F5124]/30 hover:scale-105 flex items-center justify-center gap-2"
            >
              Access App
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </a>
            <a 
              href="/shop" 
              class="w-full sm:w-auto bg-white/10 backdrop-blur-sm text-white border border-white/20 px-8 py-4 rounded-full font-bold text-lg hover:bg-white/20 transition-all duration-300 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
              </svg>
              Go to Shop
            </a>
          </div>

          <!-- Trust Row -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-12 pt-8 border-t border-white/10 animate-fade-in stagger-4">
            <div v-for="item in trustItems" :key="item.label" class="flex items-center gap-3 justify-center lg:justify-start">
              <div class="w-10 h-10 rounded-xl bg-[#9F5124]/10 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon"/>
                </svg>
              </div>
              <span class="text-sm font-medium text-white/80">{{ item.label }}</span>
            </div>
          </div>
        </div>

        <!-- Right Visual -->
        <div class="relative animate-fade-in stagger-2">
          <!-- Main Image Container -->
          <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-[#1a2332] to-[#0d1520] border border-white/10 shadow-2xl">
            <img 
              src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=800&h=600&fit=crop" 
              alt="Modern warehouse with organized inventory" 
              class="w-full h-[400px] lg:h-[500px] object-cover opacity-90"
              loading="eager"
            />
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0B1115]/80 via-transparent to-transparent"></div>
            
            <!-- Floating Card -->
            <div class="absolute bottom-6 left-6 right-6 bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-4">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#9F5124] flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                </div>
                <div>
                  <p class="text-white font-semibold">Trusted Distribution</p>
                  <p class="text-white/60 text-sm">Quality products, reliable delivery</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Decorative Elements -->
          <div class="absolute -top-4 -right-4 w-24 h-24 bg-[#9F5124]/20 rounded-2xl blur-xl"></div>
          <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-[#9F5124]/15 rounded-full blur-xl"></div>
        </div>
      </div>
    </div>

    <!-- Bottom Wave -->
    <div class="absolute bottom-0 left-0 right-0">
      <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full">
        <path d="M0,64L48,69.3C96,75,192,85,288,80C384,75,480,53,576,48C672,43,768,53,864,64C960,75,1056,85,1152,80C1248,75,1344,53,1392,42.7L1440,32V120H0Z" class="fill-[#F1EFEE]"/>
      </svg>
    </div>
  </section>
</template>

<script setup>
const trustItems = [
  { label: 'Genuine Products', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
  { label: 'Competitive Prices', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  { label: 'Reliable Delivery', icon: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0' },
  { label: 'Built for Businesses', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
]
</script>
