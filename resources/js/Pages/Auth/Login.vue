<template>
  <div class="min-h-screen flex items-center justify-center px-4" :style="{ background: 'var(--bg)' }">
    <div class="w-full max-w-md">
      <!-- Theme Toggle -->
      <div class="flex justify-end mb-6">
        <button @click="toggleTheme" class="p-2.5 rounded-xl transition-all" :style="{ background: 'var(--surface)', border: '1px solid var(--border)', color: theme === 'dark' ? '#fbbf24' : 'var(--text-secondary)' }" :title="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'">
          <svg v-if="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
        </button>
      </div>

      <!-- Logo & Title -->
      <div class="text-center mb-8">
        <img v-if="theme === 'dark'" src="/images/logo_light.png" alt="Supply 4 Me" class="h-14 w-auto mx-auto mb-4" />
        <img v-else src="/images/logo_dark.png" alt="Supply 4 Me" class="h-14 w-auto mx-auto mb-4" />
        <h1 class="text-xl font-bold" :style="{ color: 'var(--text)' }">SUPPLY 4 ME</h1>
        <p class="text-sm mt-1" :style="{ color: 'var(--text-muted)' }">ERP Management System</p>
      </div>

      <!-- Login Card -->
      <div class="p-8" :style="{ background: 'var(--surface-strong)', border: '1px solid var(--border)', borderRadius: 'var(--radius)', boxShadow: 'var(--shadow-lg)', backdropFilter: 'blur(var(--glass-blur))' }">
        <h2 class="text-lg font-semibold mb-1" :style="{ color: 'var(--text)' }">Welcome back</h2>
        <p class="text-sm mb-6" :style="{ color: 'var(--text-muted)' }">Sign in to your account</p>

        <div
          v-if="form.errors.email"
          class="text-sm px-4 py-3 rounded-xl mb-6"
          :style="{ background: 'var(--danger-soft)', color: 'var(--danger)', border: '1px solid var(--danger)' }"
        >
          {{ form.errors.email }}
        </div>

        <form @submit.prevent="submit">
          <div class="mb-4">
            <label class="form-label" for="email">Email</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              class="form-input"
              placeholder="you@example.com"
              required
            />
            <p v-if="form.errors.email" class="mt-1 text-xs" :style="{ color: 'var(--danger)' }">{{ form.errors.email }}</p>
          </div>

          <div class="mb-4">
            <label class="form-label" for="password">Password</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              class="form-input"
              placeholder="Enter your password"
              required
            />
            <p v-if="form.errors.password" class="mt-1 text-xs" :style="{ color: 'var(--danger)' }">{{ form.errors.password }}</p>
          </div>

          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="form.remember"
                type="checkbox"
                class="w-4 h-4 rounded"
                :style="{ accentColor: 'var(--brand)' }"
              />
              <span class="text-sm" :style="{ color: 'var(--text-secondary)' }">Remember me</span>
            </label>
          </div>

          <button
            type="submit"
            class="btn btn-primary w-full py-2.5"
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

      <!-- Footer -->
      <p class="text-center text-xs mt-6" :style="{ color: 'var(--text-muted)' }">"Moving business forward, together."</p>
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