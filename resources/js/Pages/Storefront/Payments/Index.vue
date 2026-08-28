<template>
  <AccountLayout :cartCount="cartCount" :customer="customer">
    <h1 class="text-2xl font-bold text-[var(--color-text)] mb-6">My Payments</h1>

    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-xs text-[var(--color-text-secondary)] mb-1">Total Payments</p>
        <p class="text-xl font-bold text-[var(--color-text)]">₦{{ Number(summary.total).toLocaleString() }}</p>
      </div>
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-xs text-[var(--color-text-secondary)] mb-1">Completed</p>
        <p class="text-xl font-bold text-green-600 dark:text-green-400">₦{{ Number(summary.completed).toLocaleString() }}</p>
      </div>
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-4 dark:bg-gray-800 dark:border-gray-700">
        <p class="text-xs text-[var(--color-text-secondary)] mb-1">Pending</p>
        <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">₦{{ Number(summary.pending).toLocaleString() }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
      <input v-model="search" type="text" placeholder="Search by payment number..."
        class="flex-1 px-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
      <select v-model="statusFilter"
        class="px-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>

    <!-- Empty state -->
    <div v-if="payments.data?.length === 0" class="text-center py-12">
      <svg class="w-16 h-16 mx-auto text-[var(--color-text-secondary)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
      <p class="text-lg font-medium text-[var(--color-text)] mb-1">No payments found</p>
      <p class="text-sm text-[var(--color-text-secondary)]">{{ search || statusFilter ? 'Try adjusting your filters' : 'No payments recorded yet.' }}</p>
    </div>

    <!-- Payment cards -->
    <div v-else class="space-y-4">
      <div v-for="pmt in payments.data" :key="pmt.id"
        class="bg-white rounded-2xl border border-[var(--color-border)] p-5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
          <div>
            <h3 class="font-bold text-[var(--color-text)]">{{ pmt.payment_number }}</h3>
            <p class="text-xs text-[var(--color-text-secondary)]">{{ new Date(pmt.payment_date || pmt.created_at).toLocaleDateString('en-NG', { year: 'numeric', month: 'short', day: 'numeric' }) }}</p>
          </div>
          <span class="self-start text-xs px-2.5 py-1 rounded-full font-medium"
            :class="{
              'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': pmt.status === 'completed',
              'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': pmt.status === 'pending',
              'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': pmt.status === 'rejected',
            }">
            {{ pmt.status }}
          </span>
        </div>

        <div class="flex items-center justify-between">
          <div>
            <p class="text-lg font-bold text-[var(--color-text)]">₦{{ Number(pmt.amount).toLocaleString() }}</p>
            <p class="text-xs text-[var(--color-text-secondary)] capitalize">{{ pmt.payment_method?.replace('_', ' ') }}</p>
          </div>
          <div v-if="pmt.receipt_path" class="text-right">
            <a :href="`/storage/${pmt.receipt_path}`" target="_blank"
              class="text-xs text-accent hover:text-accent-hover transition-colors">View Receipt</a>
          </div>
        </div>

        <div v-if="pmt.reference_number" class="mt-2 pt-2 border-t border-[var(--color-border)] dark:border-gray-700 text-xs text-[var(--color-text-secondary)]">
          Reference: {{ pmt.reference_number }}
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="payments.last_page > 1" class="flex justify-center gap-2 mt-6">
      <button v-for="p in payments.last_page" :key="p" @click="goToPage(p)"
        class="px-3 py-1 rounded-full text-sm font-medium transition-colors cursor-pointer"
        :class="p === payments.current_page ? 'bg-accent text-white' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'">
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
  payments: Object,
  summary: { type: Object, default: () => ({ total: 0, completed: 0, pending: 0 }) },
  customer: Object,
  cartCount: { type: Number, default: 0 },
  filters: Object,
})

const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')

function applyFilters() {
  router.get('/account/payments', {
    search: search.value || undefined,
    status: statusFilter.value || undefined,
  }, { preserveState: true, replace: true })
}

watch(search, () => applyFilters())
watch(statusFilter, () => applyFilters())

function goToPage(page) {
  router.get('/account/payments', {
    page,
    search: search.value || undefined,
    status: statusFilter.value || undefined,
  }, { preserveState: true, replace: true })
}
</script>
