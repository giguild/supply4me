<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">About <span class="text-[#9F5124]">Supply 4 Me</span></h1>
          <p class="text-white/60 max-w-2xl mx-auto text-lg">Nigeria's trusted B2B platform for quality FMCG products</p>
        </div>
      </section>

      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 space-y-10">

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-2xl font-bold text-[#2D2C2C] mb-4">Our Story</h2>
            <p class="text-[#616262] leading-relaxed mb-4">
              Supply 4 Me was founded with a simple mission: to make it easy for businesses across Nigeria to access quality FMCG (Fast-Moving Consumer Goods) at competitive wholesale prices.
            </p>
            <p class="text-[#616262] leading-relaxed">
              We understand the challenges businesses face in sourcing reliable products. That's why we've built a platform that connects retailers, wholesalers, and distributors with a wide range of genuine products — all from the comfort of your office or store.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-2xl font-bold text-[#2D2C2C] mb-4">Our Mission</h2>
            <p class="text-[#616262] leading-relaxed">
              To empower Nigerian businesses by providing a seamless, transparent, and reliable platform for sourcing quality FMCG products at the best prices, with fast delivery to your doorstep.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-2xl font-bold text-[#2D2C2C] mb-6">Why Choose Us?</h2>
            <div class="grid sm:grid-cols-2 gap-6">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#9F5124]/10 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                  <h3 class="font-bold text-[#2D2C2C] mb-1">Genuine Products</h3>
                  <p class="text-sm text-[#616262]">Every product is sourced from verified suppliers and manufacturers.</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#9F5124]/10 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <div>
                  <h3 class="font-bold text-[#2D2C2C] mb-1">Competitive Prices</h3>
                  <p class="text-sm text-[#616262]">Direct from manufacturers — no middlemen, better prices.</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#9F5124]/10 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                </div>
                <div>
                  <h3 class="font-bold text-[#2D2C2C] mb-1">Fast Delivery</h3>
                  <p class="text-sm text-[#616262]">Reliable logistics partners ensure your orders arrive on time.</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#9F5124]/10 flex items-center justify-center shrink-0">
                  <svg class="w-5 h-5 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                  <h3 class="font-bold text-[#2D2C2C] mb-1">Built for Businesses</h3>
                  <p class="text-sm text-[#616262]">Minimum order quantities and bulk pricing designed for businesses.</p>
                </div>
              </div>
            </div>
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

defineProps({
  cartCount: { type: Number, default: 0 },
})
</script>
