<template>
  <AccountLayout :cartCount="cartCount" :customer="customer">
    <h1 class="text-2xl font-bold text-[var(--color-text)] mb-6">My Invoices</h1>

    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-xs text-[var(--color-text-secondary)] mb-1">Total Invoiced</p>
        <p class="text-xl font-bold text-[var(--color-text)]">₦{{ Number(summary.total).toLocaleString() }}</p>
      </div>
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-xs text-[var(--color-text-secondary)] mb-1">Total Paid</p>
        <p class="text-xl font-bold text-green-600 dark:text-green-400">₦{{ Number(summary.paid).toLocaleString() }}</p>
      </div>
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-xs text-[var(--color-text-secondary)] mb-1">Total Due</p>
        <p class="text-xl font-bold text-accent">₦{{ Number(summary.due).toLocaleString() }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
      <input v-model="search" type="text" placeholder="Search by invoice number..."
        class="flex-1 px-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
      <select v-model="statusFilter"
        class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="sent">Sent</option>
        <option value="partial">Partial</option>
        <option value="paid">Paid</option>
        <option value="overdue">Overdue</option>
      </select>
    </div>

    <!-- Empty state -->
    <div v-if="invoices.data?.length === 0" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-[var(--color-text-secondary)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" /></svg>
      <p class="text-lg font-medium text-[var(--color-text)] mb-1">No invoices found</p>
      <p class="text-sm text-[var(--color-text-secondary)]">{{ search || statusFilter ? 'Try adjusting your filters' : 'No invoices yet.' }}</p>
    </div>

    <!-- Invoice cards -->
    <div v-else class="space-y-4">
      <div v-for="inv in invoices.data" :key="inv.id"
        class="bg-white rounded-2xl border border-[var(--color-border)] p-5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
          <div>
            <h3 class="font-bold text-[var(--color-text)]">{{ inv.invoice_number }}</h3>
            <p class="text-xs text-[var(--color-text-secondary)]">{{ new Date(inv.invoice_date || inv.created_at).toLocaleDateString('en-NG', { year: 'numeric', month: 'short', day: 'numeric' }) }}</p>
          </div>
          <span class="self-start text-xs px-2.5 py-1 rounded-full font-medium"
            :class="{
              'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': inv.status === 'paid',
              'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': inv.status === 'partial' || inv.status === 'pending',
              'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': inv.status === 'overdue',
            }">
            {{ inv.status }}
          </span>
        </div>

        <!-- Amount breakdown -->
        <div class="grid grid-cols-3 gap-4 text-sm mb-3">
          <div>
            <p class="text-[var(--color-text-secondary)] text-xs">Total</p>
            <p class="font-bold text-[var(--color-text)]">₦{{ Number(inv.total_amount).toLocaleString() }}</p>
          </div>
          <div>
            <p class="text-[var(--color-text-secondary)] text-xs">Paid</p>
            <p class="font-bold text-green-600 dark:text-green-400">₦{{ Number(inv.paid_amount || 0).toLocaleString() }}</p>
          </div>
          <div>
            <p class="text-[var(--color-text-secondary)] text-xs">Due</p>
            <p class="font-bold" :class="inv.due_amount > 0 ? 'text-accent' : 'text-green-600 dark:text-green-400'">₦{{ Number(inv.due_amount || 0).toLocaleString() }}</p>
          </div>
        </div>

        <!-- Progress bar -->
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-3">
          <div class="bg-green-500 h-1.5 rounded-full transition-all" :style="{ width: Math.min(((inv.paid_amount || 0) / inv.total_amount) * 100, 100) + '%' }"></div>
        </div>

        <div class="flex items-center justify-end gap-2">
          <a v-if="inv.status !== 'paid'" :href="`/payment/${inv.id}`"
            class="text-xs bg-accent text-white px-3 py-1.5 rounded-full hover:bg-accent-hover transition-colors">Upload Payment</a>
          <span v-if="inv.status === 'paid'"
            class="text-xs bg-green-100 text-green-700 px-3 py-1.5 rounded-full dark:bg-green-900/30 dark:text-green-400">Fully Paid</span>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="invoices.last_page > 1" class="flex justify-center gap-2 mt-6">
      <button v-for="p in invoices.last_page" :key="p" @click="goToPage(p)"
        class="px-3 py-1 rounded-full text-sm font-medium transition-colors cursor-pointer"
        :class="p === invoices.current_page ? 'bg-accent text-white' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'">
        {{ p }}
      </button>
    </div>
  </AccountLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AccountLayout from '@/Pages/Storefront/AccountLayout.vue'

const props = defineProps({
  invoices: Object,
  summary: { type: Object, default: () => ({ total: 0, paid: 0, due: 0 }) },
  customer: Object,
  cartCount: { type: Number, default: 0 },
  filters: Object,
})

const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')

function applyFilters() {
  router.get('/account/invoices', {
    search: search.value || undefined,
    status: statusFilter.value || undefined,
  }, { preserveState: true, replace: true })
}

watch(search, () => applyFilters())
watch(statusFilter, () => applyFilters())

function goToPage(page) {
  router.get('/account/invoices', {
    page,
    search: search.value || undefined,
    status: statusFilter.value || undefined,
  }, { preserveState: true, replace: true })
}
</script>
