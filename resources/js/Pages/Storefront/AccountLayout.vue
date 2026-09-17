<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="customer" />

    <main>
      <!-- Hero Banner -->
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center gap-4">
            <!-- Avatar -->
            <div class="relative group cursor-pointer shrink-0" @click="$refs.avatarInput.click()">
              <div v-if="customerAvatar" class="w-16 h-16 rounded-full overflow-hidden border-2 border-white/20">
                <img :src="customerAvatar" class="w-full h-full object-cover" alt="Avatar" />
              </div>
              <div v-else class="w-16 h-16 rounded-full bg-[#9F5124]/20 flex items-center justify-center text-[#9F5124] font-bold text-xl border-2 border-white/20">
                {{ customer.name?.charAt(0) || '?' }}
              </div>
              <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
              </div>
              <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="uploadAvatar" />
            </div>

            <div>
              <h1 class="text-2xl sm:text-3xl font-extrabold text-white">{{ customer.name }}</h1>
              <p class="text-white/60 text-sm">{{ customer.email }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
          <!-- Tabs -->
          <div class="flex gap-1 mb-8 overflow-x-auto scrollbar-hide border-b border-[#2D2C2C]/10">
            <a
              v-for="tab in tabs"
              :key="tab.key"
              :href="tab.href"
              class="px-5 py-3 text-sm font-semibold whitespace-nowrap transition-all border-b-2 -mb-px"
              :class="activeTab === tab.key
                ? 'border-[#9F5124] text-[#9F5124]'
                : 'border-transparent text-[#616262] hover:text-[#2D2C2C] hover:border-[#2D2C2C]/20'"
            >
              <span class="flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon"/>
                </svg>
                {{ tab.label }}
              </span>
            </a>
          </div>

          <slot />
        </div>
      </section>
    </main>

    <MarketingFooter :customer="customer" :cartCount="cartCount" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

const props = defineProps({
  cartCount: { type: Number, default: 0 },
  customer: Object,
})

const page = usePage()

const customerAvatar = computed(() => {
  return props.customer?.avatar ? `/storage/${props.customer.avatar}` : null
})

function uploadAvatar(event) {
  const file = event.target.files[0]
  if (!file) return
  const formData = new FormData()
  formData.append('avatar', file)
  router.post('/account/avatar', formData, {
    onFinish: () => {}
  })
}

const tabs = [
  { key: 'profile', label: 'Profile', href: '/account', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
  { key: 'orders', label: 'Orders', href: '/account/orders', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
  { key: 'invoices', label: 'Invoices', href: '/account/invoices', icon: 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z' },
  { key: 'payments', label: 'Payments', href: '/account/payments', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
]

const activeTab = computed(() => {
  const url = page.url
  if (url === '/account' || url.startsWith('/account?')) return 'profile'
  if (url.startsWith('/account/orders')) return 'orders'
  if (url.startsWith('/account/invoices')) return 'invoices'
  if (url.startsWith('/account/payments')) return 'payments'
  return 'profile'
})
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>
