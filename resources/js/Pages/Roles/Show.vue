<template>
  <AppLayout :user="$page.props.auth.user">
    <PageHeader :title="role.name" subtitle="Role details and assigned permissions">
      <template #actions>
        <Link :href="route('roles.edit', role.id)"
          class="bg-accent text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-accent-hover transition-colors">
          Edit Role
        </Link>
      </template>
    </PageHeader>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Role Info -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">Role Information</h3>
        <div class="space-y-3">
          <div>
            <p class="text-xs text-[var(--color-text-secondary)]">Name</p>
            <p class="font-medium text-[var(--color-text)]">{{ role.name }}</p>
          </div>
          <div>
            <p class="text-xs text-[var(--color-text-secondary)]">Guard</p>
            <p class="font-medium text-[var(--color-text)]">{{ role.guard_name }}</p>
          </div>
          <div>
            <p class="text-xs text-[var(--color-text-secondary)]">Total Permissions</p>
            <p class="font-medium text-[var(--color-text)]">{{ role.permissions?.length || 0 }}</p>
          </div>
          <div>
            <p class="text-xs text-[var(--color-text-secondary)]">Assigned Users</p>
            <p class="font-medium text-[var(--color-text)]">{{ role.users?.length || 0 }}</p>
          </div>
        </div>
      </div>

      <!-- Permissions by Module -->
      <div class="lg:col-span-2 bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">Permissions</h3>

        <div v-if="Object.keys(permissionsByModule).length === 0" class="text-center py-8">
          <p class="text-[var(--color-text-secondary)]">No permissions assigned to this role.</p>
        </div>

        <div v-else class="space-y-6">
          <div v-for="(perms, module) in permissionsByModule" :key="module">
            <h4 class="text-sm font-bold text-[var(--color-text)] uppercase tracking-wide mb-2">{{ module }}</h4>
            <div class="flex flex-wrap gap-2">
              <span v-for="perm in perms" :key="perm.id"
                class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-accent/10 text-accent border border-accent/30">
                {{ perm.name.split('.')[1] }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Assigned Users -->
      <div class="lg:col-span-3 bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-[var(--color-text)] mb-4">Assigned Users</h3>

        <div v-if="!role.users?.length" class="text-center py-8">
          <p class="text-[var(--color-text-secondary)]">No users assigned to this role.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-[var(--color-border)] dark:border-gray-700">
                <th class="text-left px-4 py-2 font-semibold text-[var(--color-text)]">Name</th>
                <th class="text-left px-4 py-2 font-semibold text-[var(--color-text)]">Email</th>
                <th class="text-left px-4 py-2 font-semibold text-[var(--color-text)]">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in role.users" :key="user.id"
                class="border-b border-[var(--color-border)] dark:border-gray-700 last:border-b-0">
                <td class="px-4 py-3 font-medium text-[var(--color-text)]">{{ user.name }}</td>
                <td class="px-4 py-3 text-[var(--color-text-secondary)]">{{ user.email }}</td>
                <td class="px-4 py-3">
                  <span class="text-xs px-2 py-0.5 rounded-full"
                    :class="user.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'">
                    {{ user.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import PageHeader from '@/Components/UI/PageHeader.vue'

const props = defineProps({
  role: Object,
  permissionsByModule: Object,
})
</script>
