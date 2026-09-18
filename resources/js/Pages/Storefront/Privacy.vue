<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">Privacy <span class="text-[#9F5124]">Policy</span></h1>
          <p class="text-white/60">Last updated: September 2026</p>
        </div>
      </section>

      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 space-y-8">

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">1. Information We Collect</h2>
            <p class="text-[#616262] text-sm leading-relaxed mb-3">We collect the following types of information:</p>
            <ul class="space-y-2 text-[#616262] text-sm leading-relaxed">
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> <strong class="text-[#2D2C2C]">Personal Information:</strong> Name, email address, phone number, delivery address</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> <strong class="text-[#2D2C2C]">Business Information:</strong> Company name, business type, tax identification</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> <strong class="text-[#2D2C2C]">Transaction Data:</strong> Order history, payment receipts, delivery records</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> <strong class="text-[#2D2C2C]">Usage Data:</strong> Browsing activity, device information, IP address</li>
            </ul>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">2. How We Use Your Information</h2>
            <ul class="space-y-2 text-[#616262] text-sm leading-relaxed">
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> To process and fulfil your orders</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> To communicate order updates and delivery status</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> To provide customer support</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> To improve our platform and services</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> To send promotional communications (with your consent)</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> To comply with legal obligations</li>
            </ul>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">3. Data Sharing</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              We do not sell your personal data. We may share your information with logistics partners for delivery purposes, payment processors for transaction verification, and legal authorities when required by law.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">4. Data Security</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              We implement industry-standard security measures to protect your data, including encryption, secure server infrastructure, and regular security audits. However, no method of transmission over the internet is 100% secure.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">5. Your Rights</h2>
            <ul class="space-y-2 text-[#616262] text-sm leading-relaxed">
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Access and review your personal data</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Request correction of inaccurate data</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Request deletion of your data</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Opt out of marketing communications</li>
            </ul>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">6. Contact Us</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              For any questions about this Privacy Policy, please contact us at <strong class="text-[#9F5124]">privacy@supply4me.ng</strong>.
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
