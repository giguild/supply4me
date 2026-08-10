<template>
  <AppLayout :user="$page.props.auth.user">
      <PageHeader
        title="Profile Settings"
        subtitle="Manage your personal information and password"
      />

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Personal Information</h3>

          <div
            v-if="profileForm.recentlySuccessful"
            class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-6"
          >
            Profile updated successfully!
          </div>

          <form @submit.prevent="submitProfile">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                <input
                  v-model="profileForm.name"
                  type="text"
                  class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                  :class="{ 'border-red-400 focus:ring-red-500': profileForm.errors.name }"
                />
                <p v-if="profileForm.errors.name" class="mt-1 text-xs text-red-500">
                  {{ profileForm.errors.name }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                <input
                  v-model="profileForm.email"
                  type="email"
                  class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                  :class="{ 'border-red-400 focus:ring-red-500': profileForm.errors.email }"
                />
                <p v-if="profileForm.errors.email" class="mt-1 text-xs text-red-500">
                  {{ profileForm.errors.email }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                <input
                  v-model="profileForm.phone"
                  type="text"
                  class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                  :class="{ 'border-red-400 focus:ring-red-500': profileForm.errors.phone }"
                />
                <p v-if="profileForm.errors.phone" class="mt-1 text-xs text-red-500">
                  {{ profileForm.errors.phone }}
                </p>
              </div>
            </div>

            <div class="mt-6">
              <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="profileForm.processing"
              >
                <span v-if="profileForm.processing" class="inline-flex items-center gap-2">
                  <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                  </svg>
                  Saving...
                </span>
                <span v-else>Save Changes</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 h-fit">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Change Password</h3>

          <div
            v-if="passwordForm.recentlySuccessful"
            class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl mb-6"
          >
            Password updated successfully!
          </div>

          <form @submit.prevent="submitPassword">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Current Password</label>
                <input
                  v-model="passwordForm.current_password"
                  type="password"
                  class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                  :class="{ 'border-red-400 focus:ring-red-500': passwordForm.errors.current_password }"
                />
                <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-red-500">
                  {{ passwordForm.errors.current_password }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                <input
                  v-model="passwordForm.password"
                  type="password"
                  class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                  :class="{ 'border-red-400 focus:ring-red-500': passwordForm.errors.password }"
                />
                <p v-if="passwordForm.errors.password" class="mt-1 text-xs text-red-500">
                  {{ passwordForm.errors.password }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                <input
                  v-model="passwordForm.password_confirmation"
                  type="password"
                  class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                />
              </div>
            </div>

            <div class="mt-6">
              <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                :disabled="passwordForm.processing"
              >
                <span v-if="passwordForm.processing" class="inline-flex items-center gap-2">
                  <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                  </svg>
                  Updating...
                </span>
                <span v-else>Update Password</span>
              </button>
            </div>
          </form>
        </div>
      </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue'
import PageHeader from '@/Components/UI/PageHeader.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  user: Object,
})

const toast = useToast()

const profileForm = useForm({
  name: props.user?.name || '',
  email: props.user?.email || '',
  phone: props.user?.phone || '',
})

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submitProfile = () => {
  profileForm.put(route('profile.update'), {
    onSuccess: () => {
      toast.success('Profile updated successfully!')
    },
    onError: () => {
      toast.error('Failed to update profile. Please check the errors.')
    },
  })
}

const submitPassword = () => {
  passwordForm.put(route('profile.password.update'), {
    onSuccess: () => {
      toast.success('Password updated successfully!')
      passwordForm.reset()
    },
    onError: () => {
      toast.error('Failed to update password. Please check the errors.')
    },
  })
}
</script>
