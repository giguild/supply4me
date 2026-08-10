<template>
  <StorefrontLayout :cartCount="cartCount" :customer="$page.props.auth?.customer" currentPage="account">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-[var(--color-text)] mb-2">Upload Payment</h1>
      <p class="text-[var(--color-text-secondary)] mb-8">Upload your payment receipt for Order #{{ order.order_number }}</p>

      <!-- Order Summary -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
          <span class="text-[var(--color-text-secondary)]">Amount Due</span>
          <span class="text-2xl font-bold text-accent">₦{{ Number(order.total_amount).toLocaleString() }}</span>
        </div>
        <div class="text-sm text-[var(--color-text-secondary)]">
          <p>Order: {{ order.order_number }}</p>
          <p>Invoice: {{ invoice?.invoice_number || 'N/A' }}</p>
        </div>
      </div>

      <!-- Upload Form -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <form @submit.prevent="submitPayment">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Amount Paid (₦)</label>
              <input v-model.number="form.amount" type="number" step="0.01" :max="order.total_amount" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
              <p v-if="errors.amount" class="text-red-500 text-xs mt-1">{{ errors.amount }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Reference Number (optional)</label>
              <input v-model="form.reference_number" type="text" class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="M-Pesa code, transaction ID..." />
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
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  order: Object,
  invoice: Object,
  cartCount: { type: Number, default: 0 },
})

const form = useForm({
  amount: props.order.total_amount,
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
  form.post(`/payment/${props.order.id}`, {
    onFinish: () => { processing.value = false },
    onError: (e) => { errors.value = e },
  })
}
</script>
