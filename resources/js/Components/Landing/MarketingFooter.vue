<template>
  <!-- Mobile Bottom Nav (authenticated only) -->
  <nav v-if="customer" class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-[#2D2C2C]/10 z-50" style="padding-bottom: env(safe-area-inset-bottom, 0px)">
    <div class="grid grid-cols-5 gap-0">
      <a href="/" class="flex flex-col items-center py-2.5 text-[#616262] hover:text-[#9F5124] transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Home</span>
      </a>
      <a href="/shop" class="flex flex-col items-center py-2.5 text-[#616262] hover:text-[#9F5124] transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Shop</span>
      </a>
      <a href="/cart" class="flex flex-col items-center py-2.5 text-[#616262] hover:text-[#9F5124] transition-colors relative">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
        </svg>
        <span v-if="cartCount > 0" class="absolute top-1.5 right-2 bg-[#9F5124] text-white text-[9px] rounded-full min-w-[16px] h-4 flex items-center justify-center font-bold px-1">{{ cartCount > 99 ? '99+' : cartCount }}</span>
        <span class="text-[10px] mt-0.5 font-medium">Cart</span>
      </a>
      <a href="/account" class="flex flex-col items-center py-2.5 text-[#616262] hover:text-[#9F5124] transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Account</span>
      </a>
      <a href="/wishlist" class="flex flex-col items-center py-2.5 text-[#616262] hover:text-[#9F5124] transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span class="text-[10px] mt-0.5 font-medium">Wishlist</span>
      </a>
    </div>
  </nav>

  <!-- Bottom spacing for mobile nav -->
  <div v-if="customer" class="sm:hidden h-16" />

  <!-- Full Footer -->
  <footer class="bg-[#0B1115] text-white pt-20 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 lg:gap-12 mb-16">
        <!-- Brand Column -->
        <div class="col-span-2 md:col-span-4 lg:col-span-2">
          <a href="/" class="inline-block mb-6">
            <img src="/images/logo_light.png" alt="Supply 4 Me" class="h-10" />
          </a>
          <p class="text-white/60 mb-6 max-w-sm leading-relaxed">
            Supply 4 Me is a technology-driven FMCG distribution platform connecting suppliers, retailers and communities for a stronger, more efficient supply chain across Nigeria.
          </p>
          <div class="flex items-center gap-4">
            <a v-for="social in socials" :key="social.name" :href="social.url" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-[#9F5124] transition-colors" :aria-label="social.name">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" v-html="social.icon"></svg>
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="font-bold text-white mb-6">Quick Links</h4>
          <ul class="space-y-3">
            <li v-for="link in quickLinks" :key="link.label">
              <a :href="link.href" class="text-white/60 hover:text-[#9F5124] transition-colors text-sm">{{ link.label }}</a>
            </li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h4 class="font-bold text-white mb-6">Support</h4>
          <ul class="space-y-3">
            <li v-for="link in supportLinks" :key="link.label">
              <a :href="link.href" class="text-white/60 hover:text-[#9F5124] transition-colors text-sm">{{ link.label }}</a>
            </li>
          </ul>
        </div>

        <!-- Get Started -->
        <div>
          <h4 class="font-bold text-white mb-6">Get Started</h4>
          <p class="text-white/60 text-sm mb-6">
            Access the Supply 4 Me app and grow your business today.
          </p>
          <a 
            href="/store-login" 
            class="inline-flex items-center gap-2 bg-[#9F5124] text-white px-6 py-3 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25"
          >
            Access App
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
          </a>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-white/40 text-sm">
          &copy; {{ currentYear }} Supply 4 Me. All rights reserved.
        </p>
        <p class="text-white/40 text-sm italic">
          Reliable Supply Chains. A Brighter Tomorrow.
        </p>
      </div>
    </div>
  </footer>

  <InstallBanner />
</template>

<script setup>
import InstallBanner from '@/Components/PWA/InstallBanner.vue'

defineProps({
  customer: { type: Object, default: null },
  cartCount: { type: Number, default: 0 },
})

const currentYear = new Date().getFullYear()

const socials = [
  { name: 'Facebook', url: '#', icon: '<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>' },
  { name: 'Twitter', url: '#', icon: '<path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>' },
  { name: 'Instagram', url: '#', icon: '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>' },
  { name: 'LinkedIn', url: '#', icon: '<path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/>' },
]

const quickLinks = [
  { label: 'Home', href: '/' },
  { label: 'Shop', href: '/shop' },
  { label: 'About Us', href: '/about' },
  { label: 'Contact', href: '/contact' },
]

const supportLinks = [
  { label: 'Help Centre', href: '/help-centre' },
  { label: 'Returns & Refunds', href: '/returns' },
  { label: 'Terms & Conditions', href: '/terms' },
  { label: 'Privacy Policy', href: '/privacy' },
]
</script>
