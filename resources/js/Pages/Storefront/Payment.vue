<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <!-- Hero Banner -->
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-[#9F5124]/20 flex items-center justify-center">
              <svg class="w-6 h-6 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl sm:text-3xl font-extrabold text-white">Upload Payment</h1>
              <p class="text-white/60 text-sm">Invoice #{{ invoice.invoice_number }}</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

          <!-- Invoice Summary -->
          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 mb-6">
            <div class="flex justify-between items-center mb-5">
              <span class="text-[#616262] text-sm">Amount Due</span>
              <span class="text-2xl font-extrabold text-[#9F5124]">₦{{ Number(invoice.due_amount).toLocaleString() }}</span>
            </div>
            <div class="space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-[#616262]">Invoice</span>
                <span class="font-semibold text-[#2D2C2C]">{{ invoice.invoice_number }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#616262]">Total</span>
                <span class="font-semibold text-[#2D2C2C]">₦{{ Number(invoice.total_amount).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#616262]">Already Paid</span>
                <span class="font-semibold text-green-600">₦{{ Number(invoice.paid_amount).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between border-t border-[#2D2C2C]/5 pt-3">
                <span class="text-[#616262]">Balance Due</span>
                <span class="font-bold text-[#9F5124]">₦{{ Number(invoice.due_amount).toLocaleString() }}</span>
              </div>
            </div>
          </div>

          <!-- Bank Account Details -->
          <div v-if="company?.bank_name" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 mb-6">
            <h2 class="text-lg font-bold text-[#2D2C2C] mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
              Make Payment To
            </h2>
            <div class="bg-[#F1EFEE] rounded-xl p-4 space-y-3">
              <div class="flex justify-between">
                <span class="text-[#616262] text-sm">Bank Name</span>
                <span class="font-semibold text-[#2D2C2C] text-sm">{{ company.bank_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#616262] text-sm">Account Name</span>
                <span class="font-semibold text-[#2D2C2C] text-sm">{{ company.bank_account_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#616262] text-sm">Account Number</span>
                <span class="font-bold text-[#9F5124] text-sm">{{ company.bank_account_number }}</span>
              </div>
            </div>
          </div>

          <!-- Payment History -->
          <div v-if="invoice.payments?.length" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 mb-6">
            <h2 class="text-lg font-bold text-[#2D2C2C] mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Payment History
            </h2>
            <div class="space-y-3">
              <div v-for="pmt in invoice.payments" :key="pmt.id" class="flex items-center justify-between p-3 bg-[#F1EFEE] rounded-xl">
                <div>
                  <p class="text-sm font-semibold text-[#2D2C2C]">{{ pmt.payment_number }}</p>
                  <p class="text-xs text-[#616262]">{{ new Date(pmt.payment_date).toLocaleDateString() }}</p>
                </div>
                <div class="text-right">
                  <p class="font-bold text-[#2D2C2C]">₦{{ Number(pmt.amount).toLocaleString() }}</p>
                  <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="{
                    'bg-amber-100 text-amber-700': pmt.status === 'pending',
                    'bg-green-100 text-green-700': pmt.status === 'completed',
                    'bg-red-100 text-red-700': pmt.status === 'rejected',
                  }">{{ pmt.status }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload Form -->
          <div v-if="!isPaid" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6">
            <h2 class="text-lg font-bold text-[#2D2C2C] mb-5 flex items-center gap-2">
              <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
              </svg>
              Submit Payment
            </h2>

            <form @submit.prevent="submitPayment">
              <div class="space-y-5">
                <!-- Amount -->
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Amount Paid (₦)</label>
                  <div class="relative">
                    <input
                      v-model.number="form.amount"
                      type="number"
                      step="0.01"
                      :max="invoice.due_amount"
                      min="0.01"
                      required
                      class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm"
                    />
                    <span class="absolute left-3.5 top-3 text-[#616262] text-sm font-medium">₦</span>
                  </div>
                  <p class="text-xs text-[#616262] mt-1.5">Maximum: ₦{{ Number(invoice.due_amount).toLocaleString() }}</p>
                  <p v-if="errors.amount" class="text-red-500 text-xs mt-1.5">{{ errors.amount }}</p>
                </div>

                <!-- Reference -->
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Reference Number (optional)</label>
                  <div class="relative">
                    <input
                      v-model="form.reference_number"
                      type="text"
                      placeholder="Transaction ID, transfer reference..."
                      class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] placeholder-[#616262] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm"
                    />
                    <svg class="absolute left-3.5 top-3 h-4 w-4 text-[#616262]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                  </div>
                </div>

                <!-- Receipt Upload -->
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Payment Receipt *</label>
                  <input type="file" @change="handleFile" accept=".jpg,.jpeg,.png,.pdf" class="hidden" ref="fileInput" />
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="w-full border-2 border-dashed border-[#2D2C2C]/15 rounded-xl p-6 text-center hover:border-[#9F5124]/40 hover:bg-[#9F5124]/5 transition-all duration-300"
                    :class="form.receipt ? 'border-[#9F5124]/40 bg-[#9F5124]/5' : ''"
                  >
                    <div v-if="form.receipt" class="flex items-center justify-center gap-3">
                      <svg class="w-8 h-8 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <div class="text-left">
                        <p class="font-semibold text-[#2D2C2C] text-sm">{{ form.receipt.name }}</p>
                        <p class="text-xs text-[#616262]">Click to change</p>
                      </div>
                    </div>
                    <div v-else>
                      <svg class="w-10 h-10 text-[#616262]/40 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                      </svg>
                      <p class="text-sm font-semibold text-[#9F5124]">Choose file</p>
                      <p class="text-xs text-[#616262] mt-1">JPG, PNG, PDF — max 5MB</p>
                    </div>
                  </button>
                  <p v-if="errors.receipt" class="text-red-500 text-xs mt-1.5">{{ errors.receipt }}</p>
                </div>
              </div>

              <!-- Submit -->
              <button
                type="submit"
                :disabled="processing || !form.receipt"
                class="w-full mt-6 bg-[#9F5124] text-white py-3.5 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25 disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <span v-if="processing" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                {{ processing ? 'Uploading...' : 'Submit Payment' }}
              </button>
            </form>
          </div>

          <!-- Fully Paid Message -->
          <div v-else class="bg-white rounded-2xl border border-green-200 p-8 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-green-50 flex items-center justify-center">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-[#2D2C2C] mb-2">Invoice Fully Paid</h3>
            <p class="text-[#616262] text-sm">This invoice has been settled in full. No further payments can be uploaded.</p>
          </div>

          <!-- Actions -->
          <div class="flex gap-4 mt-8">
            <a href="/shop" class="flex-1 text-center bg-white border border-[#2D2C2C]/10 text-[#2D2C2C] py-3.5 rounded-full font-semibold hover:border-[#9F5124]/40 hover:text-[#9F5124] transition-all duration-300">
              Continue Shopping
            </a>
            <a href="/account" class="flex-1 text-center bg-[#9F5124] text-white py-3.5 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25">
              View Account
            </a>
          </div>
        </div>
      </section>
    </main>

    <MarketingFooter :customer="$page.props.auth?.customer" :cartCount="cartCount" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

const props = defineProps({
  invoice: Object,
  isPaid: Boolean,
  company: Object,
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
