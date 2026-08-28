<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Notifications" subtitle="Stay updated on your activity">
            <template #actions>
                <button v-if="unreadCount > 0" @click="markAllRead" class="text-sm bg-accent text-white px-4 py-2 rounded-full hover:bg-accent-hover transition-colors">
                    Mark all as read
                </button>
            </template>
        </PageHeader>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-[var(--color-border)] p-3 sm:p-4 mb-4">
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-center">
                <div class="flex-1 min-w-0 sm:min-w-[200px]">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        <input
                            v-model="searchQuery"
                            @input="debouncedSearch"
                            type="text"
                            placeholder="Search notifications..."
                            class="w-full pl-10 pr-4 py-2 rounded-xl border border-[var(--color-border)] bg-white text-[var(--color-text)] text-sm focus:ring-2 focus:ring-accent focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        />
                    </div>
                </div>
                <div class="flex gap-3">
                    <select
                        v-model="filterType"
                        @change="applyFilters"
                        class="flex-1 sm:flex-none px-3 py-2 rounded-xl border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All types</option>
                        <option v-for="t in types" :key="t" :value="t">{{ formatType(t) }}</option>
                    </select>
                    <select
                        v-model="filterStatus"
                        @change="applyFilters"
                        class="flex-1 sm:flex-none px-3 py-2 rounded-xl border border-[var(--color-border)] bg-white text-sm text-[var(--color-text)] focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >
                        <option value="">All</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                </div>
                <button v-if="hasActiveFilters" @click="clearFilters" class="text-sm text-accent hover:text-accent/80 font-medium">
                    Clear filters
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-[var(--color-border)] overflow-hidden">
            <div v-if="notifications.data?.length === 0" class="text-center py-16">
                <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400">No notifications found</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                    {{ hasActiveFilters ? 'Try adjusting your filters.' : "You'll be notified when something happens." }}
                </p>
            </div>

            <div v-else>
                <div
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                    :class="{ 'bg-accent/5': !isRead(notification) }"
                >
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                        :class="iconClass(notification.icon)">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath(notification.icon)" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                {{ notification.title }}
                            </h4>
                            <span v-if="!isRead(notification)" class="w-2 h-2 rounded-full bg-accent shrink-0"></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5 line-clamp-2">
                            {{ notification.message }}
                        </p>
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mt-2">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ timeAgo(notification.created_at) }}</span>
                            <Link
                                v-if="notification.action_url"
                                :href="notification.action_url"
                                class="text-xs text-accent hover:text-accent/80 font-medium"
                            >
                                {{ notification.action_label || 'View' }}
                            </Link>
                            <button
                                v-if="!isRead(notification)"
                                @click="markAsRead(notification)"
                                class="text-xs text-accent hover:text-accent/80 font-medium"
                            >
                                Mark as read
                            </button>
                            <button
                                @click="deleteNotification(notification.id)"
                                class="text-xs text-gray-400 hover:text-red-500 transition-colors ml-auto"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="notifications.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between px-4 sm:px-6 py-4 gap-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ notifications.from }}-{{ notifications.to }} of {{ notifications.total }}
                    </p>
                    <div class="flex items-center gap-1">
                        <button
                            v-for="page in visiblePages"
                            :key="page"
                            @click="goToPage(page)"
                            class="w-8 h-8 rounded-lg text-sm font-medium transition-colors"
                            :class="page === notifications.current_page ? 'bg-accent text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import PageHeader from '@/Components/UI/PageHeader.vue'

const props = defineProps({
    notifications: Object,
    unreadCount: Number,
    types: Array,
    filters: Object,
})

const searchQuery = ref(props.filters?.search || '')
const filterType = ref(props.filters?.type || '')
const filterStatus = ref(props.filters?.status || '')

const hasActiveFilters = computed(() => searchQuery.value || filterType.value || filterStatus.value)

const visiblePages = computed(() => {
    const pages = []
    const current = props.notifications.current_page
    const last = props.notifications.last_page
    const start = Math.max(1, current - 2)
    const end = Math.min(last, current + 2)
    for (let i = start; i <= end; i++) pages.push(i)
    return pages
})

let searchTimeout = null
const debouncedSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => applyFilters(), 300)
}

const applyFilters = () => {
    const params = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (filterType.value) params.type = filterType.value
    if (filterStatus.value) params.status = filterStatus.value
    router.get(route('notifications.index'), params, { preserveState: true, replace: true })
}

const clearFilters = () => {
    searchQuery.value = ''
    filterType.value = ''
    filterStatus.value = ''
    router.get(route('notifications.index'), {}, { replace: true })
}

const isRead = (notification) => {
    return notification.recipients?.[0]?.read_at != null
}

const formatType = (type) => {
    return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

const markAsRead = (notification) => {
    router.put(route('notifications.mark-read', notification.id), {}, {
        preserveState: true,
        onSuccess: () => {
            if (notification.recipients?.[0]) {
                notification.recipients[0].read_at = new Date().toISOString()
            }
        }
    })
}

const markAllRead = () => {
    router.put(route('notifications.mark-all-read'))
}

const deleteNotification = (id) => {
    if (confirm('Delete this notification?')) {
        router.delete(route('notifications.destroy', id))
    }
}

const goToPage = (page) => {
    const params = { page }
    if (searchQuery.value) params.search = searchQuery.value
    if (filterType.value) params.type = filterType.value
    if (filterStatus.value) params.status = filterStatus.value
    router.get(route('notifications.index'), params, { preserveState: true })
}

const iconPath = (icon) => {
    const paths = {
        'shopping-cart': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
        'check-circle': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x-circle': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'credit-card': 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'file-text': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        'alert-triangle': 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'truck': 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
        'package': 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    }
    return paths[icon] || 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
}

const iconClass = (icon) => {
    const classes = {
        'shopping-cart': 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        'check-circle': 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        'x-circle': 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        'credit-card': 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
        'file-text': 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        'alert-triangle': 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
        'truck': 'bg-cyan-100 text-cyan-600 dark:bg-cyan-900/30 dark:text-cyan-400',
        'package': 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
    }
    return classes[icon] || 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
}

const timeAgo = (date) => {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000)
    if (seconds < 60) return 'just now'
    const minutes = Math.floor(seconds / 60)
    if (minutes < 60) return `${minutes}m ago`
    const hours = Math.floor(minutes / 60)
    if (hours < 24) return `${hours}h ago`
    const days = Math.floor(hours / 24)
    if (days < 7) return `${days}d ago`
    return new Date(date).toLocaleDateString()
}
</script>
