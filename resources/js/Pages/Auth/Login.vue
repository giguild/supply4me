<template>
  <div class="min-h-screen flex items-center justify-center px-4" :style="{ background: 'linear-gradient(135deg, #1a1f36 0%, #2d3250 50%, #1a1f36 100%)' }">
    <div class="w-full max-w-md">
      <!-- Theme Toggle -->
      <div class="flex justify-end mb-4">
        <button @click="toggleTheme" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors" :title="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'">
          <svg v-if="theme === 'dark'" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
          <svg v-else class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>
      </div>

      <!-- Logo & Title -->
      <div class="text-center mb-8">
        <img v-if="theme === 'dark'" src="/images/logo_light.png" alt="SUPPLY4ME" class="h-16 w-auto mx-auto mb-4" />
        <img v-else src="/images/logo_dark.png" alt="SUPPLY4ME" class="h-16 w-auto mx-auto mb-4" />
        <h1 class="text-2xl font-bold text-white">SUPPLY4ME</h1>
        <p class="text-gray-400 text-sm mt-1">ERP Management System</p>
      </div>

      <!-- Login Card -->
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-1">Welcome back</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Sign in to your account</p>

        <div
          v-if="form.errors.email"
          class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm px-4 py-3 rounded-xl mb-6"
        >
          {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" for="email">
              Email
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition"
              placeholder="you@example.com"
              required
            />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" for="password">
              Password
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition"
              placeholder="Enter your password"
              required
            />
            <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
          </div>

          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-accent focus:ring-accent"
              />
              <span class="text-sm text-gray-600 dark:text-gray-400">Remember me</span>
            </label>
          </div>

          <button
            type="submit"
            class="w-full bg-accent hover:bg-accent-hover text-white font-medium py-2.5 px-4 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="form.processing"
          >
            <span v-if="form.processing" class="inline-flex items-center gap-2">
              <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
              </svg>
              Signing in...
            </span>
            <span v-else>Sign In</span>
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'
import { useTheme } from '@/composables/useTheme'

const toast = useToast()
const { theme, toggleTheme } = useTheme()

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login.post'), {
    onStart: () => {
      toast.info('Signing in...')
    },
    onSuccess: () => {
      toast.success('Login successful!')
    },
    onError: () => {
      toast.error('Invalid credentials. Please try again.')
    },
  })
}
</script>
