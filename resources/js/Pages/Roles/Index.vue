<template>
  <AppLayout :user="$page.props.auth.user">
    <PageHeader title="Roles" subtitle="Manage roles and their permissions">
      <template #actions>
        <Link :href="route('roles.create')" class="bg-accent text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-accent-hover transition-colors">
          + New Role
        </Link>
      </template>
    </PageHeader>

    <!-- Filters -->
    <div class="mb-6">
      <input v-model="search" type="text" placeholder="Search roles..."
        class="w-full sm:w-80 px-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-2xl border border-[var(--color-border)] dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-[var(--color-border)] dark:border-gray-700">
              <th class="text-left px-6 py-3 font-semibold text-[var(--color-text)]">Role Name</th>
              <th class="text-left px-6 py-3 font-semibold text-[var(--color-text)]">Guard</th>
              <th class="text-center px-6 py-3 font-semibold text-[var(--color-text)]">Permissions</th>
              <th class="text-center px-6 py-3 font-semibold text-[var(--color-text)]">Users</th>
              <th class="text-right px-6 py-3 font-semibold text-[var(--color-text)]">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="role in roles.data" :key="role.id"
              class="border-b border-[var(--color-border)] dark:border-gray-700 last:border-b-0 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
              <td class="px-6 py-4">
                <Link :href="route('roles.show', role.id)" class="font-semibold text-[var(--color-text)] hover:text-accent transition-colors">
                  {{ role.name }}
                </Link>
              </td>
              <td class="px-6 py-4 text-[var(--color-text-secondary)]">{{ role.guard_name }}</td>
              <td class="px-6 py-4 text-center">
                <span class="text-xs bg-accent/10 text-accent px-2 py-1 rounded-full font-medium">
                  {{ role.permissions_count }} permissions
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <span class="text-xs bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 px-2 py-1 rounded-full font-medium">
                  {{ role.users_count }} users
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <Link :href="route('roles.edit', role.id)"
                    class="text-xs border border-[var(--color-border)] text-[var(--color-text)] px-3 py-1 rounded-full hover:border-accent transition-colors dark:border-gray-600">
                    Edit
                  </Link>
                  <button v-if="role.name !== 'super_admin'" @click="deleteRole(role)"
                    class="text-xs border border-red-300 text-red-600 px-3 py-1 rounded-full hover:bg-red-50 transition-colors dark:border-red-700 dark:text-red-400">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="roles.data?.length === 0" class="text-center py-12">
        <p class="text-[var(--color-text-secondary)]">No roles found.</p>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="roles.last_page > 1" class="flex justify-center gap-2 mt-6">
      <button v-for="p in roles.last_page" :key="p" @click="goToPage(p)"
        class="px-3 py-1 rounded-full text-sm font-medium transition-colors cursor-pointer"
        :class="p === roles.current_page ? 'bg-accent text-white' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'">
        {{ p }}
      </button>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import PageHeader from '@/Components/UI/PageHeader.vue'

const props = defineProps({
  roles: Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')

watch(search, () => {
  router.get(route('roles.index'), { search: search.value || undefined }, { preserveState: true, replace: true })
})

function goToPage(page) {
  router.get(route('roles.index'), { page, search: search.value || undefined }, { preserveState: true, replace: true })
}

function deleteRole(role) {
  if (confirm(`Are you sure you want to delete the "${role.name}" role?`)) {
    useForm().delete(route('roles.destroy', role.id))
  }
}
</script>
