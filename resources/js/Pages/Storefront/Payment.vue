<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="account">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-[var(--color-text)] mb-2">Upload Payment</h1>
      <p class="text-[var(--color-text-secondary)] mb-8">Upload your payment receipt for Invoice #{{ invoice.invoice_number }}</p>

      <!-- Invoice Summary -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
          <span class="text-[var(--color-text-secondary)]">Amount Due</span>
          <span class="text-2xl font-bold text-accent">₦{{ Number(invoice.due_amount).toLocaleString() }}</span>
        </div>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-[var(--color-text-secondary)]">Invoice</span>
            <span class="font-medium text-[var(--color-text)]">{{ invoice.invoice_number }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-[var(--color-text-secondary)]">Total</span>
            <span class="font-medium text-[var(--color-text)]">₦{{ Number(invoice.total_amount).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-[var(--color-text-secondary)]">Already Paid</span>
            <span class="font-medium text-green-600 dark:text-green-400">₦{{ Number(invoice.paid_amount).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between border-t border-[var(--color-border)] pt-2">
            <span class="text-[var(--color-text-secondary)]">Balance Due</span>
            <span class="font-bold text-accent">₦{{ Number(invoice.due_amount).toLocaleString() }}</span>
          </div>
        </div>
      </div>

      <!-- Payment History -->
      <div v-if="invoice.payments?.length" class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Payment History</h2>
        <div class="space-y-3">
          <div v-for="pmt in invoice.payments" :key="pmt.id" class="flex items-center justify-between p-3 rounded-xl bg-[var(--color-bg)] dark:bg-gray-700/50">
            <div>
              <p class="text-sm font-medium text-[var(--color-text)]">{{ pmt.payment_number }}</p>
              <p class="text-xs text-[var(--color-text-secondary)]">{{ new Date(pmt.payment_date).toLocaleDateString() }}</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-[var(--color-text)]">₦{{ Number(pmt.amount).toLocaleString() }}</p>
              <span class="text-xs px-2 py-0.5 rounded-full" :class="{
                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': pmt.status === 'pending',
                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': pmt.status === 'completed',
                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': pmt.status === 'rejected',
              }">{{ pmt.status }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Upload Form -->
      <div v-if="!isPaid" class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <form @submit.prevent="submitPayment">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Amount Paid (₦)</label>
              <input v-model.number="form.amount" type="number" step="0.01" :max="invoice.due_amount" min="0.01" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
              <p class="text-xs text-[var(--color-text-secondary)] mt-1">Maximum: ₦{{ Number(invoice.due_amount).toLocaleString() }}</p>
              <p v-if="errors.amount" class="text-red-500 text-xs mt-1">{{ errors.amount }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Reference Number (optional)</label>
              <input v-model="form.reference_number" type="text" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Transaction ID, transfer reference..." />
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Payment Receipt *</label>
              <div class="border-2 border-dashed border-[var(--color-border)] rounded-xl p-6 text-center hover:border-accent transition-colors dark:border-gray-600">
                <input type="file" @change="handleFile" accept=".jpg,.jpeg,.png,.pdf" class="hidden" ref="fileInput" />
                <button type="button" @click="$refs.fileInput.click()" class="text-accent font-semibold">
                  {{ form.receipt ? form.receipt.name : 'Choose file (JPG, PNG, PDF - max 5MB)' }}
                </button>
              </div>
              <p v-if="errors.receipt" class="text-red-500 text-xs mt-1">{{ errors.receipt }}</p>
            </div>
          </div>

          <button type="submit" :disabled="processing || !form.receipt" class="w-full mt-6 bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors disabled:opacity-50">
            {{ processing ? 'Uploading...' : 'Submit Payment' }}
          </button>
        </form>
      </div>

      <!-- Fully Paid Message -->
      <div v-else class="bg-green-50 dark:bg-green-900/20 rounded-2xl border border-green-200 dark:border-green-800 p-6 text-center">
        <svg class="w-12 h-12 text-green-600 dark:text-green-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <h3 class="text-lg font-bold text-green-700 dark:text-green-400 mb-1">Invoice Fully Paid</h3>
        <p class="text-sm text-green-600 dark:text-green-500">This invoice has been settled in full. No further payments can be uploaded.</p>
      </div>

      <div class="flex gap-4 mt-6">
        <a href="/" class="flex-1 text-center bg-white border border-[var(--color-border)] text-[var(--color-text)] py-3 rounded-full font-semibold hover:border-accent transition-colors dark:bg-gray-800 dark:border-gray-600 dark:text-white">Continue Shopping</a>
        <a href="/account" class="flex-1 text-center bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors">View Account</a>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  invoice: Object,
  isPaid: Boolean,
  cartCount: { type: Number, default: 0 },
})

const form = useForm({
  amount: props.invoice.due_amount,
  reference_number: '',
  receipt: null,
})

const processing = ref(false)
const errors = ref({})

function handleFile(e) {
  form.receipt = e.target.files[0]
}

function submitPayment() {
  processing.value = true
  errors.value = {}
  form.post(`/payment/${props.invoice.id}`, {
    onFinish: () => { processing.value = false },
    onError: (e) => { errors.value = e },
  })
}
</script>
