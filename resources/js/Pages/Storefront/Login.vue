<template>
  <StorefrontLayout :cartCount="cartCount" :customer="null" currentPage="home">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-[var(--color-text)]">Welcome Back</h1>
        <p class="text-[var(--color-text-secondary)] mt-1">Login to your account</p>
      </div>

      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-8 dark:bg-gray-800 dark:border-gray-700">
        <div v-if="$page.props.flash?.error" class="bg-red-50 text-red-700 text-sm rounded-xl p-4 mb-4 dark:bg-red-900/30 dark:text-red-400">
          {{ $page.props.flash.error }}
        </div>

        <form @submit.prevent="submit">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Email</label>
              <input v-model="form.email" type="email" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
              <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Password</label>
              <input v-model="form.password" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
            <div class="flex items-center">
              <input v-model="form.remember" type="checkbox" id="remember" class="rounded border-gray-300 text-accent focus:ring-accent" />
              <label for="remember" class="ml-2 text-sm text-[var(--color-text-secondary)]">Remember me</label>
            </div>
          </div>

          <button type="submit" :disabled="processing" class="w-full mt-6 bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors disabled:opacity-50">
            {{ processing ? 'Logging in...' : 'Login' }}
          </button>
        </form>

        <p class="text-center text-sm text-[var(--color-text-secondary)] mt-6">
          Don't have an account? <a href="/register" class="text-accent font-semibold hover:text-accent-hover transition-colors">Register</a>
        </p>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  cartCount: { type: Number, default: 0 },
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const processing = ref(false)
const errors = ref({})

function submit() {
  processing.value = true
  errors.value = {}
  form.post('/store-login', {
    onFinish: () => { processing.value = false },
    onError: (e) => { errors.value = e },
  })
}
</script>
