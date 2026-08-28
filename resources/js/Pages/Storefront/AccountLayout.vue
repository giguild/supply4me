<template>
  <StorefrontLayout :cartCount="cartCount" :customer="customer" currentPage="account">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Tabs -->
      <div class="flex gap-1 mb-6 overflow-x-auto border-b border-[var(--color-border)] dark:border-gray-700">
        <a v-for="tab in tabs" :key="tab.key" :href="tab.href"
          class="px-4 py-2.5 text-sm font-medium whitespace-nowrap transition-colors border-b-2 -mb-px"
          :class="activeTab === tab.key
            ? 'border-accent text-accent'
            : 'border-transparent text-[var(--color-text-secondary)] hover:text-[var(--color-text)] hover:border-gray-300 dark:hover:border-gray-600'"
        >
          {{ tab.label }}
        </a>
      </div>

      <slot />
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  cartCount: { type: Number, default: 0 },
  customer: Object,
})

const page = usePage()

const tabs = [
  { key: 'profile', label: 'Profile', href: '/account' },
  { key: 'orders', label: 'Orders', href: '/account/orders' },
  { key: 'invoices', label: 'Invoices', href: '/account/invoices' },
  { key: 'payments', label: 'Payments', href: '/account/payments' },
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
