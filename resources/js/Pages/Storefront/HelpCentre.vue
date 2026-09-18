<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">Help <span class="text-[#9F5124]">Centre</span></h1>
          <p class="text-white/60 max-w-2xl mx-auto text-lg">Find answers to common questions</p>
        </div>
      </section>

      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 space-y-4">
          <div v-for="(faq, i) in faqs" :key="i" class="bg-white rounded-2xl border border-[#2D2C2C]/5 overflow-hidden">
            <button @click="toggle(i)" class="w-full flex items-center justify-between p-6 text-left">
              <span class="font-bold text-[#2D2C2C]">{{ faq.q }}</span>
              <svg class="w-5 h-5 text-[#616262] shrink-0 transition-transform duration-300" :class="openIndex === i ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div v-if="openIndex === i" class="px-6 pb-6">
              <p class="text-[#616262] leading-relaxed">{{ faq.a }}</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <MarketingFooter :customer="$page.props.auth?.customer" :cartCount="cartCount" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

defineProps({
  cartCount: { type: Number, default: 0 },
})

const openIndex = ref(null)
const toggle = (i) => { openIndex.value = openIndex.value === i ? null : i }

const faqs = [
  { q: 'How do I place an order?', a: 'Simply create an account, browse our shop, add products to your cart, and proceed to checkout. You can pay via bank transfer and upload your payment receipt for confirmation.' },
  { q: 'What are the minimum order quantities?', a: 'Minimum order quantities vary by product. Each product displays its minimum order quantity on the product page.' },
  { q: 'How long does delivery take?', a: 'Delivery typically takes 2-5 business days depending on your location within Nigeria. You will receive tracking updates once your order is dispatched.' },
  { q: 'Can I return products?', a: 'Yes, we have a returns policy for damaged or incorrect items. Please contact our support team within 48 hours of receiving your order.' },
  { q: 'How do I track my order?', a: 'Once your order is shipped, you will receive a tracking number via email and SMS. You can also track your order from your account dashboard.' },
  { q: 'What payment methods are accepted?', a: 'We currently accept bank transfers. Upload your payment receipt after transfer, and our team will verify and confirm your payment.' },
  { q: 'How do I become a vendor/supplier?', a: 'Please contact our partnerships team at support@supply4me.ng with your business details and product catalogue.' },
]
</script>
