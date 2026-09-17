<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <!-- Hero Banner -->
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-green-500/10 flex items-center justify-center mb-4">
              <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-2">Order Confirmed!</h1>
            <p class="text-white/60">Thank you for your order. We'll process it shortly.</p>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

          <!-- Order Details -->
          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 mb-6">
            <div class="flex justify-between items-center mb-6 pb-4 border-b border-[#2D2C2C]/5">
              <div>
                <h2 class="text-lg font-bold text-[#2D2C2C]">Order #{{ order.order_number }}</h2>
                <p class="text-sm text-[#616262]">{{ new Date(order.created_at).toLocaleDateString() }}</p>
              </div>
              <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">{{ order.status }}</span>
            </div>

            <div class="space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-[#616262]">Subtotal</span>
                <span class="font-semibold text-[#2D2C2C]">₦{{ Number(order.subtotal).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#616262]">Tax</span>
                <span class="font-semibold text-[#2D2C2C]">₦{{ Number(order.tax_amount).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold border-t border-[#2D2C2C]/5 pt-3">
                <span class="text-[#2D2C2C]">Total</span>
                <span class="text-[#9F5124]">₦{{ Number(order.total_amount).toLocaleString() }}</span>
              </div>
            </div>
          </div>

          <!-- Invoice & Payment Status -->
          <div v-if="invoice" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6 mb-6">
            <div class="flex justify-between items-center mb-5 pb-4 border-b border-[#2D2C2C]/5">
              <h3 class="font-semibold text-[#2D2C2C]">Invoice {{ invoice.invoice_number }}</h3>
              <span class="px-3 py-1 text-xs font-semibold rounded-full" :class="{
                'bg-green-100 text-green-700': invoice.status === 'paid',
                'bg-amber-100 text-amber-700': invoice.status === 'partial',
                'bg-red-100 text-red-700': invoice.status === 'overdue',
              }">{{ invoice.status }}</span>
            </div>

            <div class="space-y-3 text-sm mb-5">
              <div class="flex justify-between">
                <span class="text-[#616262]">Total</span>
                <span class="font-semibold text-[#2D2C2C]">₦{{ Number(invoice.total_amount).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-[#616262]">Paid</span>
                <span class="font-semibold text-green-600">₦{{ Number(invoice.paid_amount).toLocaleString() }}</span>
              </div>
              <div class="flex justify-between border-t border-[#2D2C2C]/5 pt-3">
                <span class="text-[#616262]">Balance Due</span>
                <span class="font-bold" :class="invoice.due_amount > 0 ? 'text-[#9F5124]' : 'text-green-600'">₦{{ Number(invoice.due_amount).toLocaleString() }}</span>
              </div>
            </div>

            <!-- Payment History -->
            <div v-if="invoice.payments?.length" class="border-t border-[#2D2C2C]/5 pt-4">
              <h4 class="font-semibold text-[#2D2C2C] mb-3">Payment History</h4>
              <div v-for="pmt in invoice.payments" :key="pmt.id" class="flex items-center justify-between text-sm py-2.5 border-b border-[#2D2C2C]/5 last:border-0">
                <div>
                  <span class="font-medium text-[#2D2C2C]">{{ pmt.payment_number }}</span>
                  <span class="text-[#616262] ml-2">{{ new Date(pmt.payment_date).toLocaleDateString() }}</span>
                </div>
                <div class="text-right">
                  <span class="font-semibold text-[#2D2C2C]">₦{{ Number(pmt.amount).toLocaleString() }}</span>
                  <span class="text-xs px-2 py-0.5 rounded-full ml-2" :class="{
                    'bg-amber-100 text-amber-700': pmt.status === 'pending',
                    'bg-green-100 text-green-700': pmt.status === 'completed',
                    'bg-red-100 text-red-700': pmt.status === 'rejected',
                  }">{{ pmt.status }}</span>
                </div>
              </div>
            </div>

            <!-- Bank Account Details -->
            <div v-if="company?.bank_name" class="border-t border-[#2D2C2C]/5 pt-4 mt-2">
              <h4 class="font-semibold text-[#2D2C2C] mb-3">Make Payment To</h4>
              <div class="bg-[#F1EFEE] rounded-xl p-4 space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-[#616262]">Bank Name</span>
                  <span class="font-semibold text-[#2D2C2C]">{{ company.bank_name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-[#616262]">Account Name</span>
                  <span class="font-semibold text-[#2D2C2C]">{{ company.bank_account_name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-[#616262]">Account Number</span>
                  <span class="font-bold text-[#9F5124]">{{ company.bank_account_number }}</span>
                </div>
              </div>
            </div>

            <!-- Upload Link -->
            <div v-if="invoice.status !== 'paid'" class="border-t border-[#2D2C2C]/5 pt-4 mt-4">
              <p class="text-sm text-[#616262]">
                Balance of ₦{{ Number(invoice.due_amount).toLocaleString() }} remaining.
                <a :href="`/payment/${invoice.id}`" class="text-[#9F5124] font-semibold hover:text-[#8a4620] ml-1">Upload payment receipt</a>
              </p>
            </div>
            <div v-else class="border-t border-[#2D2C2C]/5 pt-4 mt-4">
              <p class="text-sm text-green-600 font-medium">This invoice has been fully paid.</p>
            </div>
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
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

const props = defineProps({
  order: Object,
  invoice: Object,
  company: Object,
  cartCount: { type: Number, default: 0 },
})
</script>
