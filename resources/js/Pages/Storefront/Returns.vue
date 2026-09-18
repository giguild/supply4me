<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">Returns & <span class="text-[#9F5124]">Refunds</span></h1>
        </div>
      </section>

      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 space-y-8">

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">1. Eligibility for Returns</h2>
            <ul class="space-y-2 text-[#616262] text-sm leading-relaxed">
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Products that are damaged during delivery</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Products that are defective or faulty upon receipt</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Products that do not match the order description</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Wrong items delivered</li>
            </ul>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">2. Return Window</h2>
            <p class="text-[#616262] text-sm leading-relaxed">
              All return requests must be submitted within <strong class="text-[#2D2C2C]">48 hours</strong> of receiving your order. Requests made after this period may not be accepted.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">3. How to Request a Return</h2>
            <ol class="space-y-2 text-[#616262] text-sm leading-relaxed list-decimal list-inside">
              <li>Log in to your account and navigate to your order history</li>
              <li>Select the order containing the item you wish to return</li>
              <li>Click "Request Return" and provide a reason with photos if applicable</li>
              <li>Our team will review your request within 24-48 hours</li>
              <li>If approved, you will receive instructions on how to return the item</li>
            </ol>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">4. Refund Process</h2>
            <p class="text-[#616262] text-sm leading-relaxed mb-3">
              Once we receive and inspect the returned item, your refund will be processed within <strong class="text-[#2D2C2C]">5-7 business days</strong>. Refunds are made to your original payment method (bank transfer).
            </p>
            <p class="text-[#616262] text-sm leading-relaxed">
              You will receive an email notification once your refund has been processed.
            </p>
          </div>

          <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
            <h2 class="text-xl font-bold text-[#2D2C2C] mb-4">5. Non-Returnable Items</h2>
            <ul class="space-y-2 text-[#616262] text-sm leading-relaxed">
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Perishable goods (food items, beverages) that have been opened</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Products damaged due to customer mishandling</li>
              <li class="flex items-start gap-2"><span class="text-[#9F5124] mt-1">•</span> Items without original packaging or tags</li>
            </ul>
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
