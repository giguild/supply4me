<template>
  <StorefrontLayout :cartCount="cartCount" :customer="null" currentPage="home">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-[var(--color-text)]">Create an Account</h1>
        <p class="text-[var(--color-text-secondary)] mt-1">Join us to start ordering</p>
      </div>

      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-8 dark:bg-gray-800 dark:border-gray-700">
        <form @submit.prevent="submit">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Full Name</label>
              <input v-model="form.name" type="text" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
              <p v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Email</label>
              <input v-model="form.email" type="email" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
              <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Phone</label>
              <input v-model="form.phone" type="text" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
              <p v-if="errors.phone" class="text-red-500 text-xs mt-1">{{ errors.phone }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Password</label>
              <input v-model="form.password" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
              <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Confirm Password</label>
              <input v-model="form.password_confirmation" type="password" required class="w-full px-4 py-2.5 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
          </div>

          <button type="submit" :disabled="processing" class="w-full mt-6 bg-accent text-white py-3 rounded-full font-semibold hover:bg-accent-hover transition-colors disabled:opacity-50">
            {{ processing ? 'Creating account...' : 'Register' }}
          </button>
        </form>

        <p class="text-center text-sm text-[var(--color-text-secondary)] mt-6">
          Already have an account? <a href="/store-login" class="text-accent font-semibold hover:text-accent-hover transition-colors">Login</a>
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
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
})

const processing = ref(false)
const errors = ref({})

function submit() {
  processing.value = true
  errors.value = {}
  form.post('/register', {
    onFinish: () => { processing.value = false },
    onError: (e) => { errors.value = e },
  })
}
</script>
