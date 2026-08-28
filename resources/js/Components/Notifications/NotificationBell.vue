<template>
    <div class="relative" ref="bellRef">
        <button @click="toggleDropdown" class="relative p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span v-if="unreadCount > 0" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div v-if="open" class="absolute right-0 mt-2 w-[calc(100vw-2rem)] sm:w-80 max-w-[20rem] bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-sm text-gray-900 dark:text-gray-100">Notifications</h3>
                    <button v-if="unreadCount > 0" @click="markAllRead" class="text-xs text-accent hover:text-accent/80 font-medium">
                        Mark all read
                    </button>
                </div>

                <div class="max-h-80 overflow-y-auto">
                    <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        No notifications yet
                    </div>
                    <div v-else>
                        <div
                            v-for="notification in notifications"
                            :key="notification.id"
                            class="px-4 py-3 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors"
                            :class="{ 'bg-accent/5': !isRead(notification) }"
                            @click="handleClick(notification)"
                        >
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                                    :class="iconClass(notification.icon)">
                                    <component :is="iconComponent(notification.icon)" class="w-4 h-4" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ notification.title }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">
                                        {{ notification.message }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ timeAgo(notification.created_at) }}
                                    </p>
                                </div>
                                <div v-if="!isRead(notification)" class="w-2 h-2 rounded-full bg-accent shrink-0 mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-2 border-t border-gray-100 dark:border-gray-700 text-center">
                    <a :href="route('notifications.index')" class="text-xs text-accent hover:text-accent/80 font-medium" @click="open = false">
                        View all notifications
                    </a>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, h } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const bellRef = ref(null)
const open = ref(false)
const unreadCount = ref(0)
const notifications = ref([])

let pollInterval = null

const isRead = (notification) => {
    return notification.recipients?.[0]?.read_at != null
}

const toggleDropdown = () => {
    open.value = !open.value
    if (open.value) {
        fetchNotifications()
    }
}

const fetchNotifications = async () => {
    try {
        const response = await fetch(route('notifications.unread-count'))
        const data = await response.json()
        unreadCount.value = data.count
        notifications.value = data.notifications || []
    } catch (e) {}
}

const markAllRead = async () => {
    try {
        await router.put(route('notifications.mark-all-read'), {}, {
            preserveState: true,
            onSuccess: () => {
                unreadCount.value = 0
                notifications.value.forEach(n => {
                    if (n.recipients?.[0]) n.recipients[0].read_at = new Date().toISOString()
                })
            }
        })
    } catch (e) {}
}

const handleClick = (notification) => {
    if (!isRead(notification)) {
        router.put(route('notifications.mark-read', notification.id), {}, {
            preserveState: true,
            onSuccess: () => {
                if (notification.recipients?.[0]) notification.recipients[0].read_at = new Date().toISOString()
                unreadCount.value = Math.max(0, unreadCount.value - 1)
            }
        })
    }
    open.value = false
    router.get(route('notifications.index'))
}

const iconComponent = (icon) => {
    const icons = {
        'shopping-cart': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z' })
        ]),
        'check-circle': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]),
        'x-circle': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' })
        ]),
        'credit-card': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })
        ]),
        'file-text': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
        ]),
        'alert-triangle': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })
        ]),
        'truck': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0' })
        ]),
        'package': () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', 'stroke-width': '2' }, [
            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', d: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' })
        ]),
    }
    return icons[icon] || icons['bell']
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

const handleClickOutside = (e) => {
    if (bellRef.value && !bellRef.value.contains(e.target)) {
        open.value = false
    }
}

onMounted(() => {
    fetchNotifications()
    pollInterval = setInterval(fetchNotifications, 30000)
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    clearInterval(pollInterval)
    document.removeEventListener('click', handleClickOutside)
})
</script>
