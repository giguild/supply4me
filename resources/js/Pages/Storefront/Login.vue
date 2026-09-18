<template>
  <div class="min-h-screen">
    <MarketingHeader />

    <main>
      <!-- Hero Banner -->
      <section class="bg-gradient-to-br from-[#0B1115] via-[#111A20] to-[#0B1115] pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <div class="inline-flex items-center gap-2 bg-[#9F5124]/10 border border-[#9F5124]/20 rounded-full px-4 py-2 mb-5">
              <svg class="w-4 h-4 text-[#9F5124]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              <span class="text-sm font-medium text-[#9F5124]">CUSTOMER LOGIN</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mb-3">
              Welcome <span class="text-[#9F5124]">Back</span>
            </h1>
            <p class="text-white/60">Login to access your account and place orders</p>
          </div>
        </div>
      </section>

      <!-- Form -->
      <section class="bg-[#F1EFEE] -mt-1 pb-16">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 pt-10">
          <div class="bg-white rounded-3xl border border-[#2D2C2C]/5 p-8 shadow-sm">
            <!-- Error -->
            <div v-if="$page.props.flash?.error" class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mb-6 flex items-start gap-3">
              <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              {{ $page.props.flash.error }}
            </div>

            <form @submit.prevent="submit">
              <div class="space-y-5">
                <!-- Email -->
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Email Address</label>
                  <div class="relative">
                    <input
                      v-model="form.email"
                      type="email"
                      required
                      placeholder="you@example.com"
                      class="w-full pl-10 pr-4 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] placeholder-[#616262] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm"
                    />
                    <svg class="absolute left-3.5 top-3 h-4 w-4 text-[#616262]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <p v-if="errors.email" class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ errors.email }}
                  </p>
                </div>

                <!-- Password -->
                <div>
                  <label class="block text-sm font-semibold text-[#2D2C2C] mb-2">Password</label>
                  <div class="relative">
                    <input
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      required
                      placeholder="Enter your password"
                      class="w-full pl-10 pr-12 py-3 rounded-xl border border-[#2D2C2C]/10 bg-[#F1EFEE] text-[#2D2C2C] placeholder-[#616262] focus:outline-none focus:ring-2 focus:ring-[#9F5124]/30 focus:border-[#9F5124] transition-all text-sm"
                    />
                    <svg class="absolute left-3.5 top-3 h-4 w-4 text-[#616262]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <button
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute right-3.5 top-2.5 text-[#616262] hover:text-[#2D2C2C] transition-colors"
                    >
                      <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                  <input
                    v-model="form.remember"
                    type="checkbox"
                    id="remember"
                    class="w-4 h-4 rounded border-[#2D2C2C]/20 text-[#9F5124] focus:ring-[#9F5124]/30"
                  />
                  <label for="remember" class="ml-2.5 text-sm text-[#616262]">Remember me</label>
                </div>
              </div>

              <!-- Submit -->
              <button
                type="submit"
                :disabled="processing"
                class="w-full mt-6 bg-[#9F5124] text-white py-3.5 rounded-full font-bold hover:bg-[#8a4620] transition-all duration-300 hover:shadow-lg hover:shadow-[#9F5124]/25 disabled:opacity-50 flex items-center justify-center gap-2"
              >
                <span v-if="processing" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                {{ processing ? 'Logging in...' : 'Login' }}
              </button>
            </form>

            <!-- Divider -->
            <div class="flex items-center gap-4 my-6">
              <div class="flex-1 h-px bg-[#2D2C2C]/10"></div>
              <span class="text-xs text-[#616262] font-medium">OR</span>
              <div class="flex-1 h-px bg-[#2D2C2C]/10"></div>
            </div>

            <!-- Register Link -->
            <p class="text-center text-sm text-[#616262]">
              Don't have an account?
              <a :href="redirect ? `/register?redirect=${redirect}` : '/register'" class="text-[#9F5124] font-semibold hover:text-[#8a4620] transition-colors">Register</a>
            </p>
          </div>

          <!-- Back to Home -->
          <p class="text-center mt-6">
            <a :href="redirect || '/'" class="text-sm text-[#616262] hover:text-[#9F5124] transition-colors flex items-center justify-center gap-1.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              {{ redirect ? 'Back to Cart' : 'Back to Home' }}
            </a>
          </p>
        </div>
      </section>
    </main>

    <MarketingFooter />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import MarketingHeader from '@/Components/Landing/MarketingHeader.vue'
import MarketingFooter from '@/Components/Landing/MarketingFooter.vue'

const props = defineProps({
  cartCount: { type: Number, default: 0 },
  redirect: { type: String, default: '' },
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
  redirect: props.redirect,
})

const processing = ref(false)
const errors = ref({})
const showPassword = ref(false)

function submit() {
  processing.value = true
  errors.value = {}
  form.post('/store-login', {
    onFinish: () => { processing.value = false },
    onError: (e) => { errors.value = e },
  })
}
</script>
