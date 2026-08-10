<template>
  <div class="min-h-screen bg-[var(--color-bg)] flex flex-col overflow-x-hidden">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-[var(--color-border)] dark:bg-gray-900/80 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <a href="/" class="flex items-center gap-3 shrink-0">
            <img v-if="theme === 'dark'" src="/images/logo_light.png" alt="SUPPLY4ME" class="h-8" />
            <img v-else src="/images/logo_dark.png" alt="SUPPLY4ME" class="h-8" />
            <span class="text-lg font-bold text-[var(--color-text)] hidden sm:block">SUPPLY4ME</span>
          </a>

          <!-- Search (desktop) -->
          <div v-if="searchable" class="flex-1 max-w-xl mx-8 hidden sm:block">
            <slot name="search" />
          </div>

          <!-- Right actions -->
          <div class="flex items-center gap-3">
            <!-- Theme toggle -->
            <button @click="toggleTheme" class="p-2 rounded-full text-[var(--color-text-secondary)] hover:text-accent hover:bg-accent-50 transition-colors dark:hover:bg-gray-700">
              <svg v-if="theme === 'dark'" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            <!-- Cart -->
            <a href="/cart" class="relative p-2 text-[var(--color-text)] hover:text-accent transition-colors">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
              <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-accent text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">{{ cartCount }}</span>
            </a>

            <!-- Auth -->
            <template v-if="customer">
              <a href="/account" class="hidden sm:block text-sm font-medium text-[var(--color-text)] hover:text-accent transition-colors">{{ customer.name }}</a>
              <form @submit.prevent="logout" class="hidden sm:block">
                <button type="submit" class="text-sm font-medium text-[var(--color-text-secondary)] hover:text-accent transition-colors">Logout</button>
              </form>
            </template>
            <template v-else>
              <a href="/store-login" class="text-sm font-medium text-[var(--color-text)] hover:text-accent transition-colors">Login</a>
              <a href="/register" class="text-sm font-medium bg-accent text-white px-4 py-2 rounded-full hover:bg-accent-hover transition-colors">Register</a>
            </template>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Mobile Footer Nav (customer logged in) -->
    <nav v-if="customer" class="sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-[var(--color-border)] dark:bg-gray-900 dark:border-gray-700 z-50 safe-bottom">
      <div class="grid grid-cols-4 gap-0">
        <a href="/" class="flex flex-col items-center py-2 text-[var(--color-text-secondary)] hover:text-accent transition-colors" :class="{ 'text-accent': currentPage === 'home' }">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          <span class="text-xs mt-0.5">Home</span>
        </a>
        <a href="/cart" class="flex flex-col items-center py-2 text-[var(--color-text-secondary)] hover:text-accent transition-colors relative" :class="{ 'text-accent': currentPage === 'cart' }">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
          <span v-if="cartCount > 0" class="absolute top-1 right-2 bg-accent text-white text-xs rounded-full h-4 w-4 flex items-center justify-center font-bold">{{ cartCount > 9 ? '9+' : cartCount }}</span>
          <span class="text-xs mt-0.5">Cart</span>
        </a>
        <a href="/account" class="flex flex-col items-center py-2 text-[var(--color-text-secondary)] hover:text-accent transition-colors" :class="{ 'text-accent': currentPage === 'account' }">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
          <span class="text-xs mt-0.5">Account</span>
        </a>
        <button @click="logout" class="flex flex-col items-center py-2 text-[var(--color-text-secondary)] hover:text-accent transition-colors">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          <span class="text-xs mt-0.5">Logout</span>
        </button>
      </div>
    </nav>

    <!-- Bottom spacing for mobile footer -->
    <div v-if="customer" class="sm:hidden h-16" />
  </div>
</template>

<script setup>
import { useTheme } from '@/composables/useTheme'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  cartCount: { type: Number, default: 0 },
  customer: { type: Object, default: null },
  currentPage: { type: String, default: 'home' },
  searchable: { type: Boolean, default: false },
})

const { theme, toggleTheme } = useTheme()

function logout() {
  router.post('/store-logout')
}
</script>

<style scoped>
.safe-bottom {
  padding-bottom: env(safe-area-inset-bottom, 0px);
}
</style>
