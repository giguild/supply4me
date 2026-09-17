<template>
  <section id="business" class="py-24 bg-[#F1EFEE] relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <!-- Left Content -->
        <div>
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#2D2C2C] mb-6 leading-tight">
            Built for
            <span class="text-[#9F5124]">Growing Businesses</span>
          </h2>
          
          <p class="text-lg text-[#616262] mb-8 leading-relaxed">
            Whether you run a small shop or a growing chain, Supply 4 Me gives you access to quality products, competitive prices and a reliable supply network across Nigeria.
          </p>

          <!-- Benefits List -->
          <ul class="space-y-4 mb-10">
            <li v-for="benefit in benefits" :key="benefit" class="flex items-start gap-3">
              <div class="w-6 h-6 rounded-full bg-[#9F5124]/10 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3.5 h-3.5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
              </div>
              <span class="text-[#2D2C2C] font-medium">{{ benefit }}</span>
            </li>
          </ul>

          <!-- CTAs -->
          <div class="flex flex-col sm:flex-row items-center gap-4">
            <a 
              href="/store-login" 
              class="w-full sm:w-auto bg-[#9F5124] text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25 flex items-center justify-center gap-2"
            >
              Access App
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
              </svg>
            </a>
            <a 
              href="/shop" 
              class="w-full sm:w-auto bg-white text-[#2D2C2C] border border-[#2D2C2C]/10 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-50 transition-all duration-300 flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
              </svg>
              Go to Shop
            </a>
          </div>
        </div>

        <!-- Right Visual -->
        <div class="relative">
          <!-- Main Image -->
          <div class="relative rounded-3xl overflow-hidden shadow-2xl">
            <img 
              src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&h=600&fit=crop" 
              alt="Business owner using Supply 4 Me" 
              class="w-full h-[400px] lg:h-[500px] object-cover"
              loading="lazy"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-[#2D2C2C]/60 via-transparent to-transparent"></div>
          </div>

          <!-- Floating Trust Cards -->
          <div class="absolute -left-4 lg:-left-8 top-1/4 bg-white rounded-2xl p-4 shadow-xl border border-[#2D2C2C]/5 max-w-[200px]">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#9F5124]/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <p class="font-bold text-[#2D2C2C]">Trusted by</p>
                <p class="text-sm text-[#616262]">Retailers Nationwide</p>
              </div>
            </div>
          </div>

          <div class="absolute -right-4 lg:-right-8 top-1/2 bg-white rounded-2xl p-4 shadow-xl border border-[#2D2C2C]/5 max-w-[200px]">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
              </div>
              <div>
                <p class="font-bold text-[#2D2C2C]">100%</p>
                <p class="text-sm text-[#616262]">Genuine Products</p>
              </div>
            </div>
          </div>

          <!-- Quote Card -->
          <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 lg:left-auto lg:translate-x-0 lg:-bottom-8 lg:right-8 bg-[#2D2C2C] text-white rounded-2xl p-6 shadow-xl max-w-[280px]">
            <svg class="w-8 h-8 text-[#9F5124] mb-3" fill="currentColor" viewBox="0 0 24 24">
              <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
            </svg>
            <p class="text-white/90 italic mb-4">Reliable Supply Chains. A Brighter Tomorrow.</p>
            <div class="flex items-center gap-3">
              <img src="/images/logo_light.png" alt="Supply 4 Me" class="h-6" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const benefits = [
  'Wide range of FMCG products',
  'Competitive wholesale prices',
  'Reliable and secure delivery',
  'Support for retailers and businesses',
  'Trusted supplier network'
]
</script>
