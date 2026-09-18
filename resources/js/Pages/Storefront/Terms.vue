<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">Terms & <span class="text-[#9F5124]">Conditions</span></h1>
          <p class="text-white/60">Last updated: September 2026</p>
        </div>
      </section>

      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 space-y-8">

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">1. Acceptance of Terms</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              By accessing and using Supply 4 Me (supply4me.ng), you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our platform.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">2. Account Registration</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              You must provide accurate and complete information when creating an account. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">3. Orders and Pricing</h2>
            <ul class="space-y-2 text-[#616262] text-sm leading-relaxed">
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> All prices are displayed in Nigerian Naira (₦) and exclude delivery fees unless stated otherwise</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Minimum order quantities apply as indicated on each product</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> We reserve the right to cancel orders due to pricing errors or stock unavailability</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Payment must be confirmed before orders are processed</li>
            </ul>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">4. Delivery</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              Delivery timeframes are estimates and not guaranteed. Supply 4 Me is not liable for delays caused by third-party logistics providers. Risk of loss passes to you upon delivery.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">5. Intellectual Property</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              All content on this platform, including logos, text, images, and software, is the property of Supply 4 Me and is protected by applicable intellectual property laws.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">6. Limitation of Liability</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              Supply 4 Me shall not be liable for any indirect, incidental, or consequential damages arising from your use of our platform or purchase of products.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">7. Changes to Terms</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              We reserve the right to modify these terms at any time. Continued use of the platform after changes constitutes acceptance of the new terms.
            </p>
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
