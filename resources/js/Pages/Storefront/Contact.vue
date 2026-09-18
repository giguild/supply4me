<template>
  <div class="min-h-screen">
    <MarketingHeader :customer="$page.props.auth?.customer" />

    <main>
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">Get in <span class="text-[#9F5124]">Touch</span></h1>
          <p class="text-white/60 max-w-2xl mx-auto text-lg">Have questions? We'd love to hear from you.</p>
        </div>
      </section>

      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
          <div class="grid md:grid-cols-2 gap-8">
            <!-- Contact Info -->
            <div class="space-y-6">
              <div v-if="settings.company_email" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-xl bg-[#9F5124]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                  </div>
                  <div>
                    <h3 class="font-bold text-[#2D2C2C]">Email Us</h3>
                    <a :href="`mailto:${settings.company_email}`" class="text-[#616262] text-sm hover:text-[#9F5124] transition-colors">{{ settings.company_email }}</a>
                  </div>
                </div>
              </div>

              <div v-if="settings.company_phone" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-xl bg-[#9F5124]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                  </div>
                  <div>
                    <h3 class="font-bold text-[#2D2C2C]">Call Us</h3>
                    <a :href="`tel:${settings.company_phone}`" class="text-[#616262] text-sm hover:text-[#9F5124] transition-colors">{{ settings.company_phone }}</a>
                  </div>
                </div>
              </div>

              <div v-if="fullAddress" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-xl bg-[#9F5124]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  </div>
                  <div>
                    <h3 class="font-bold text-[#2D2C2C]">Visit Us</h3>
                    <p class="text-[#616262] text-sm">{{ fullAddress }}</p>
                  </div>
                </div>
              </div>

              <div v-if="settings.company_name" class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-6">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-xl bg-[#9F5124]/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                  </div>
                  <div>
                    <h3 class="font-bold text-[#2D2C2C]">{{ settings.company_name }}</h3>
                    <p class="text-[#616262] text-sm">Business Hours: Mon - Fri, 9am - 5pm</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl border border-[#2D2C2C]/5 p-8">
              <h2 class="text-xl font-bold text-[#2D2C2C] mb-6">Send a Message</h2>
              <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Name</label>
                  <input v-model="form.name" type="text" required class="w-full px-4 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm" placeholder="Your name" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Email</label>
                  <input v-model="form.email" type="email" required class="w-full px-4 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm" placeholder="you@example.com" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Message</label>
                  <textarea v-model="form.message" rows="4" required class="w-full px-4 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm resize-none" placeholder="How can we help?"></textarea>
                </div>
                <button type="submit" :disabled="submitting" class="w-full bg-[#9F5124] text-white py-3 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25 disabled:opacity-50">
                  {{ submitting ? 'Sending...' : 'Send Message' }}
                </button>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>

    <MarketingFooter :customer="$page.props.auth?.customer" :cartCount="cartCount" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  cartCount: { type: Number, default: 0 },
})

const fullAddress = computed(() => {
  const parts = [props.settings.company_address, props.settings.company_city, props.settings.company_country].filter(Boolean)
  return parts.length ? parts.join(', ') : null
})

const form = ref({ name: '', email: '', message: '' })
const submitting = ref(false)

function submitForm() {
  submitting.value = true
  setTimeout(() => {
    alert('Thank you! We will get back to you shortly.')
    form.value = { name: '', email: '', message: '' }
    submitting.value = false
  }, 1500)
}
</script>
