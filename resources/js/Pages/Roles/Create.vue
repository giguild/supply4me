<template>
  <AppLayout :user="$page.props.auth.user">
    <PageHeader title="Create Role" subtitle="Define a new role with specific permissions" />

    <div class="max-w-3xl">
      <form @submit.prevent="submit">
        <!-- Role Name -->
        <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">Role Details</h3>
          <div>
            <label class="form-label">Role Name *</label>
            <input v-model="form.name" type="text" class="form-input" placeholder="e.g. warehouse_manager"
              :class="{ 'border-red-500': form.errors.name }" />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
          </div>
        </div>

        <!-- Permissions -->
        <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-[var(--color-text)]">Permissions</h3>
            <button type="button" @click="toggleAll"
              class="text-xs text-accent hover:text-accent-hover transition-colors font-medium">
              {{ allSelected ? 'Deselect All' : 'Select All' }}
            </button>
          </div>

          <div v-if="form.errors.permissions" class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
            <p class="text-sm text-red-600 dark:text-red-400">{{ form.errors.permissions }}</p>
          </div>

          <div class="space-y-6">
            <div v-for="(perms, module) in permissions" :key="module">
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-bold text-[var(--color-text)] uppercase tracking-wide">{{ module }}</h4>
                <button type="button" @click="toggleModule(module)"
                  class="text-xs text-accent hover:text-accent-hover transition-colors">
                  {{ isModuleSelected(module) ? 'Deselect' : 'Select' }} All
                </button>
              </div>
              <div class="flex flex-wrap gap-2">
                <label v-for="perm in perms" :key="perm.id"
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium cursor-pointer transition-colors"
                  :class="form.permissions.includes(perm.name)
                    ? 'bg-accent/10 text-accent border border-accent/30'
                    : 'bg-gray-100 text-gray-600 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'">
                  <input type="checkbox" :value="perm.name" v-model="form.permissions" class="sr-only" />
                  {{ perm.name.split('.')[1] }}
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
          <Link :href="route('roles.index')"
            class="px-6 py-2.5 rounded-full border border-[var(--color-border)] text-[var(--color-text)] hover:bg-gray-50 transition-colors dark:border-gray-600 dark:hover:bg-gray-700">
            Cancel
          </Link>
          <button type="submit" :disabled="form.processing"
            class="px-6 py-2.5 rounded-full bg-accent text-white font-semibold hover:bg-accent-hover transition-colors disabled:opacity-50">
            {{ form.processing ? 'Creating...' : 'Create Role' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import PageHeader from '@/Components/UI/PageHeader.vue'

const props = defineProps({
  permissions: Object,
})

const form = useForm({
  name: '',
  permissions: [],
})

const allSelected = computed(() => {
  const allPerms = Object.values(props.permissions).flat()
  return allPerms.length > 0 && allPerms.every(p => form.permissions.includes(p.name))
})

function toggleAll() {
  if (allSelected.value) {
    form.permissions = []
  } else {
    form.permissions = Object.values(props.permissions).flat().map(p => p.name)
  }
}

function isModuleSelected(module) {
  const perms = props.permissions[module] || []
  return perms.every(p => form.permissions.includes(p.name))
}

function toggleModule(module) {
  const perms = props.permissions[module] || []
  if (isModuleSelected(module)) {
    form.permissions = form.permissions.filter(p => !perms.map(x => x.name).includes(p))
  } else {
    const newPerms = perms.map(p => p.name).filter(p => !form.permissions.includes(p))
    form.permissions = [...form.permissions, ...newPerms]
  }
}

function submit() {
  form.post(route('roles.store'))
}
</script>
