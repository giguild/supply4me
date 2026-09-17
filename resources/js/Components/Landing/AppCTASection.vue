<template>
  <section class="py-24 bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-[#9F5124]/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
        <!-- Left Content -->
        <div class="text-center lg:text-left">
          <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-6 leading-tight">
            Take Supply 4 Me
            <span class="block text-[#9F5124]">With You Everywhere</span>
          </h2>
          
          <p class="text-lg text-white/70 mb-8 max-w-xl mx-auto lg:mx-0">
            Access your account, place orders, track deliveries and grow your business — all in one app.
          </p>

          <!-- CTAs -->
          <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
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

          <!-- Trust Badges -->
          <div class="flex items-center gap-6 mt-8 justify-center lg:justify-start">
            <div class="flex items-center gap-2 text-white/60">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span class="text-sm">Secure Platform</span>
            </div>
            <div class="flex items-center gap-2 text-white/60">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span class="text-sm">24/7 Support</span>
            </div>
          </div>
        </div>

        <!-- Right Visual - Phone Mockup -->
        <div class="relative flex justify-center lg:justify-end">
          <div class="relative">
            <!-- Phone Frame -->
            <div class="relative w-[280px] h-[560px] bg-[#1a1a1a] rounded-[40px] border-4 border-[#2a2a2a] shadow-2xl overflow-hidden">
              <!-- Notch -->
              <div class="absolute top-0 left-1/2 -translate-x-1/2 w-32 h-6 bg-[#1a1a1a] rounded-b-2xl z-10"></div>
              
              <!-- Screen Content -->
              <div class="absolute inset-2 rounded-[32px] overflow-hidden bg-gradient-to-b from-[#9F5124] to-[#7a3d1a]">
                <!-- App Header -->
                <div class="pt-12 px-6">
                  <img src="/images/logo_light.png" alt="Supply 4 Me" class="h-8 mx-auto mb-6" />
                  <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-4 mb-4">
                    <p class="text-white/80 text-sm mb-1">Welcome back!</p>
                    <p class="text-white font-bold text-lg">Business Dashboard</p>
                  </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="px-6 grid grid-cols-2 gap-3">
                  <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                    <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                      </svg>
                    </div>
                    <p class="text-white text-sm font-medium">Orders</p>
                  </div>
                  <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                    <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                      </svg>
                    </div>
                    <p class="text-white text-sm font-medium">Products</p>
                  </div>
                  <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                    <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                      </svg>
                    </div>
                    <p class="text-white text-sm font-medium">Deliveries</p>
                  </div>
                  <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                    <div class="w-10 h-10 mx-auto mb-2 bg-white/20 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                      </svg>
                    </div>
                    <p class="text-white text-sm font-medium">Analytics</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Decorative Elements -->
            <div class="absolute -top-8 -right-8 w-32 h-32 bg-[#9F5124]/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-[#9F5124]/15 rounded-full blur-2xl"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
