<template>
  <header 
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="scrolled ? 'bg-[#0B1115]/95 backdrop-blur-md border-b border-white/10' : 'bg-transparent'"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16 lg:h-20">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-3 shrink-0">
          <img src="/images/logo_light.png" alt="Supply 4 Me" class="h-8 lg:h-10" />
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center gap-8">
          <a 
            v-for="item in navItems" 
            :key="item.href"
            :href="item.href" 
            class="text-sm font-medium text-white/70 hover:text-white transition-colors relative group"
          >
            {{ item.label }}
            <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-[#9F5124] transition-all duration-300 group-hover:w-full"></span>
          </a>
        </nav>

        <!-- Desktop Actions -->
        <div class="hidden lg:flex items-center gap-4">
          <a href="/shop" class="text-sm font-medium text-white/80 hover:text-white transition-colors">
            Go to Shop
          </a>

          <!-- Authenticated: User Menu -->
          <template v-if="customer">
            <a 
              href="/account" 
              class="flex items-center gap-2.5 bg-white/10 backdrop-blur-sm border border-white/15 text-white pl-2.5 pr-4 py-1.5 rounded-full hover:bg-white/20 transition-all duration-300"
            >
              <div class="w-7 h-7 rounded-full bg-[#9F5124] flex items-center justify-center text-white text-xs font-bold shrink-0">
                {{ customer.name?.charAt(0) || '?' }}
              </div>
              <span class="text-sm font-medium truncate max-w-[120px]">{{ customer.name }}</span>
            </a>
            <form @submit.prevent="logout">
              <button type="submit" class="text-sm font-medium text-white/60 hover:text-white transition-colors">
                Logout
              </button>
            </form>
          </template>

          <!-- Guest: Access App -->
          <a 
            v-else
            href="/store-login" 
            class="bg-[#9F5124] text-white px-5 py-2.5 rounded-full text-sm font-semibold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25"
          >
            Access App
          </a>
        </div>

        <!-- Mobile Menu Button -->
        <button 
          @click="mobileOpen = !mobileOpen"
          class="lg:hidden p-2 text-white/80 hover:text-white transition-colors"
          aria-label="Toggle menu"
        >
          <svg v-if="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div 
        v-if="mobileOpen" 
        class="lg:hidden bg-[#0B1115]/98 backdrop-blur-xl border-t border-white/10"
      >
        <nav class="max-w-7xl mx-auto px-4 py-6 space-y-4">
          <a 
            v-for="item in navItems" 
            :key="item.href"
            :href="item.href" 
            class="block text-base font-medium text-white/80 hover:text-white transition-colors py-2"
            @click="mobileOpen = false"
          >
            {{ item.label }}
          </a>
          <div class="pt-4 border-t border-white/10 space-y-3">
            <a 
              href="/shop" 
              class="block text-center text-white/80 hover:text-white py-2.5 transition-colors"
              @click="mobileOpen = false"
            >
              Go to Shop
            </a>

            <!-- Authenticated: User Card + Logout -->
            <template v-if="customer">
              <a 
                href="/account" 
                class="flex items-center gap-3 bg-white/10 backdrop-blur-sm border border-white/15 rounded-xl p-3 hover:bg-white/20 transition-all"
                @click="mobileOpen = false"
              >
                <div class="w-10 h-10 rounded-full bg-[#9F5124] flex items-center justify-center text-white font-bold shrink-0">
                  {{ customer.name?.charAt(0) || '?' }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-white font-semibold text-sm truncate">{{ customer.name }}</p>
                  <p class="text-white/50 text-xs truncate">{{ customer.email }}</p>
                </div>
                <svg class="w-4 h-4 text-white/40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </a>
              <form @submit.prevent="logout">
                <button type="submit" class="w-full flex items-center justify-center gap-2 text-white/60 hover:text-white py-2.5 transition-colors text-sm font-medium">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                  </svg>
                  Logout
                </button>
              </form>
            </template>

            <!-- Guest: Access App -->
            <a 
              v-else
              href="/store-login" 
              class="block text-center bg-[#9F5124] text-white px-5 py-3 rounded-full font-semibold hover:bg-[#8a4620] transition-all"
              @click="mobileOpen = false"
            >
              Access App
            </a>
          </div>
        </nav>
      </div>
    </Transition>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({
  customer: { type: Object, default: null },
})

const scrolled = ref(false)
const mobileOpen = ref(false)

function logout() {
  router.post('/store-logout')
}

const navItems = [
  { label: 'Home', href: '/' },
  { label: 'Shop', href: '/shop' },
  { label: 'For Businesses', href: '#business' },
  { label: 'About Us', href: '#about' },
  { label: 'Contact', href: '#contact' },
]

function handleScroll() {
  scrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true })
  handleScroll()
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>
