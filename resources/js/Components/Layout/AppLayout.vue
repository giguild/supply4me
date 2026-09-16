<template>
    <div class="min-h-screen flex" :style="{ background: 'var(--bg)' }">
        <!-- Sidebar -->
        <aside
            class="sidebar-panel sticky top-0 h-screen hidden lg:flex flex-col transition-all duration-300 z-40 shrink-0"
            :class="sidebarCollapsed ? 'w-[72px]' : 'w-[260px]'"
            :style="{ background: 'var(--sidebar-bg)', backdropFilter: 'blur(var(--glass-blur))', borderRight: '1px solid var(--border)' }"
        >
            <!-- Logo -->
            <div class="flex items-center gap-2.5 px-4 h-16 shrink-0" :style="{ borderBottom: '1px solid var(--border)' }">
                <Link :href="route('dashboard')" class="flex items-center gap-2.5 shrink-0">
                    <img v-if="theme === 'dark'" src="/images/logo_light.png" alt="Supply4Me" class="h-8 w-auto" />
                    <img v-else src="/images/logo_dark.png" alt="Supply4Me" class="h-8 w-auto" />
                </Link>
                <transition name="fade">
                    <div v-if="!sidebarCollapsed" class="overflow-hidden">
                        <span class="font-bold text-sm tracking-tight" :style="{ color: 'var(--text)' }">SUPPLY 4 ME</span>
                        <p class="text-[9px] font-medium uppercase tracking-[0.15em]" :style="{ color: 'var(--text-muted)' }">Moving Business Forward</p>
                    </div>
                </transition>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto py-4 px-2.5 space-y-1">
                <template v-for="item in mainNav" :key="item.route">
                    <Link :href="route(item.route)" :title="item.label"
                        class="sidebar-link"
                        :class="[isActive(item.route) ? 'sidebar-link--active' : '']"
                    >
                        <span v-html="item.icon" class="w-[18px] h-[18px] shrink-0" />
                        <transition name="fade">
                            <span v-if="!sidebarCollapsed" class="truncate text-[13px]">{{ item.label }}</span>
                        </transition>
                    </Link>
                </template>

                <!-- Section dividers + collapsible groups -->
                <template v-for="group in moreNavGroups" :key="group.label">
                    <div class="pt-4 pb-1 px-3" v-if="!sidebarCollapsed">
                        <span class="text-[10px] font-bold uppercase tracking-[0.12em]" :style="{ color: 'var(--text-muted)' }">{{ group.label }}</span>
                    </div>
                    <div v-if="sidebarCollapsed" class="pt-2 pb-1 flex justify-center">
                        <div class="w-5 h-px" :style="{ background: 'var(--border)' }"></div>
                    </div>

                    <template v-if="sidebarCollapsed">
                        <Link v-for="item in group.items" :key="item.route" :href="route(item.route)" :title="item.label"
                            class="sidebar-link justify-center"
                            :class="[isActive(item.route) ? 'sidebar-link--active' : '']"
                        >
                            <span v-html="item.icon" class="w-[18px] h-[18px] shrink-0" />
                        </Link>
                    </template>
                    <template v-else>
                        <Link v-for="item in group.items" :key="item.route" :href="route(item.route)" :title="item.label"
                            class="sidebar-link"
                            :class="[isActive(item.route) ? 'sidebar-link--active' : '']"
                        >
                            <span v-html="item.icon" class="w-[18px] h-[18px] shrink-0" />
                            <span class="truncate text-[13px]">{{ item.label }}</span>
                        </Link>
                    </template>
                </template>
            </nav>

            <!-- Footer -->
            <div class="shrink-0 px-2.5 py-2.5" :style="{ borderTop: '1px solid var(--border)' }">
                <Link v-if="isSuperAdmin" :href="route('settings.index')" title="Settings"
                    class="sidebar-link"
                    :class="[isActive('settings.index') ? 'sidebar-link--active' : '']"
                >
                    <span v-html="icons.settings" class="w-[18px] h-[18px] shrink-0" />
                    <span v-if="!sidebarCollapsed" class="truncate text-[13px]">Settings</span>
                </Link>
                <Link v-if="isSuperAdmin" :href="route('settings.payment-info')" title="Payment Info"
                    class="sidebar-link"
                    :class="[isActive('settings.payment-info') ? 'sidebar-link--active' : '']"
                >
                    <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span v-if="!sidebarCollapsed" class="truncate text-[13px]">Payment Info</span>
                </Link>
                <button @click="sidebarCollapsed = !sidebarCollapsed"
                    class="sidebar-link w-full justify-center lg:justify-start"
                >
                    <svg class="w-[18px] h-[18px] shrink-0 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                    <span v-if="!sidebarCollapsed" class="truncate text-[13px]">Collapse</span>
                </button>
            </div>
        </aside>

        <!-- Main Area -->
        <div class="flex-1 min-w-0 flex flex-col">
            <!-- Top Bar -->
            <header class="sticky top-0 z-50 h-16 flex items-center px-4 sm:px-6 lg:px-8 gap-4 shrink-0 transition-colors duration-200"
                :style="{ background: 'var(--bg-elevated)', backdropFilter: 'blur(var(--glass-blur))', borderBottom: '1px solid var(--border)' }">
                <!-- Mobile hamburger -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg" :style="{ color: 'var(--text-secondary)' }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>

                <!-- Search -->
                <button @click="showSearch = true" class="hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm w-64 lg:w-80 transition-all"
                    :style="{ background: 'var(--surface)', border: '1px solid var(--border)', color: 'var(--text-muted)' }">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <span class="flex-1 text-left">Search orders, customers...</span>
                    <kbd class="hidden lg:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-semibold rounded" :style="{ background: 'var(--border)', color: 'var(--text-muted)' }">Ctrl K</kbd>
                </button>

                <div class="flex-1"></div>

                <div class="flex items-center gap-1 ml-auto">
                    <!-- Theme toggle -->
                    <button @click="toggleTheme" class="p-2.5 rounded-xl transition-all" :style="{ color: theme === 'dark' ? '#fbbf24' : 'var(--text-secondary)' }" :title="theme === 'dark' ? 'Light mode' : 'Dark mode'">
                        <svg v-if="theme === 'dark'" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </button>

                    <!-- Notifications -->
                    <NotificationBell />

                    <!-- User -->
                    <div class="relative">
                        <button @click="showUserMenu = !showUserMenu" class="flex items-center gap-2 pl-3 pr-2 py-1.5 rounded-xl transition-all ml-1"
                            :style="{ border: '1px solid var(--border)' }">
                            <div v-if="avatarUrl" class="w-7 h-7 rounded-full overflow-hidden flex-shrink-0">
                                <img :src="avatarUrl" :alt="user?.name" class="w-full h-full object-cover" />
                            </div>
                            <div v-else class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0" :style="{ background: 'var(--brand-soft)', color: 'var(--brand)' }">
                                {{ user?.name?.charAt(0) || 'U' }}
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-[13px] font-semibold leading-tight" :style="{ color: 'var(--text)' }">{{ user?.name }}</p>
                                <p class="text-[11px] leading-tight" :style="{ color: 'var(--text-muted)' }">{{ userRole }}</p>
                            </div>
                            <svg class="w-3.5 h-3.5 shrink-0" :style="{ color: 'var(--text-muted)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <transition name="dropdown">
                            <div v-if="showUserMenu" class="absolute right-0 top-full mt-2 w-48 py-1.5 z-50"
                                :style="{ background: 'var(--surface-strong)', border: '1px solid var(--border)', borderRadius: 'var(--radius-sm)', boxShadow: 'var(--shadow-lg)' }">
                                <Link :href="route('profile.edit')" class="dropdown-item">Profile</Link>
                                <Link v-if="isSuperAdmin" :href="route('settings.index')" class="dropdown-item">Settings</Link>
                                <hr class="my-1.5" :style="{ borderColor: 'var(--border)' }" />
                                <button @click="logout" class="dropdown-item w-full text-left" :style="{ color: 'var(--danger)' }">Logout</button>
                            </div>
                        </transition>
                    </div>
                </div>
            </header>

            <!-- Mobile Nav Drawer -->
            <transition name="slide-left">
                <div v-if="mobileMenuOpen" class="lg:hidden fixed inset-0 z-50 flex">
                    <div class="absolute inset-0 bg-black/40" @click="mobileMenuOpen = false"></div>
                    <div class="relative w-72 h-full overflow-y-auto py-4 px-3" :style="{ background: 'var(--sidebar-bg)', backdropFilter: 'blur(20px)' }">
                        <div class="flex items-center gap-2 px-3 mb-4">
                            <img v-if="theme === 'dark'" src="/images/logo_light.png" class="h-7" />
                            <img v-else src="/images/logo_dark.png" class="h-7" />
                            <span class="font-bold text-sm" :style="{ color: 'var(--text)' }">SUPPLY 4 ME</span>
                        </div>
                        <Link v-for="item in mainNav" :key="item.route" :href="route(item.route)" @click="mobileMenuOpen = false"
                            class="sidebar-link"
                            :class="[isActive(item.route) ? 'sidebar-link--active' : '']">
                            <span v-html="item.icon" class="w-[18px] h-[18px] shrink-0" />
                            <span class="text-[13px]">{{ item.label }}</span>
                        </Link>
                        <template v-for="group in moreNavGroups" :key="group.label">
                            <div class="pt-4 pb-1 px-3">
                                <span class="text-[10px] font-bold uppercase tracking-[0.12em]" :style="{ color: 'var(--text-muted)' }">{{ group.label }}</span>
                            </div>
                            <Link v-for="item in group.items" :key="item.route" :href="route(item.route)" @click="mobileMenuOpen = false"
                                class="sidebar-link"
                                :class="[isActive(item.route) ? 'sidebar-link--active' : '']">
                                <span v-html="item.icon" class="w-[18px] h-[18px] shrink-0" />
                                <span class="text-[13px]">{{ item.label }}</span>
                            </Link>
                        </template>
                    </div>
                </div>
            </transition>

            <!-- Page Content -->
            <main class="flex-1 min-w-0 px-4 sm:px-6 lg:px-8 py-6 overflow-x-hidden">
                <slot />
            </main>
        </div>

        <!-- Search Modal (Ctrl+K) -->
        <div v-if="showSearch" class="fixed inset-0 z-[100] flex items-start justify-center pt-[15vh]" @click.self="showSearch = false">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showSearch = false"></div>
            <div class="relative w-full max-w-lg mx-4 overflow-hidden"
                :style="{ background: 'var(--surface-strong)', border: '1px solid var(--border)', borderRadius: 'var(--radius)', boxShadow: 'var(--shadow-lg)' }">
                <div class="flex items-center gap-3 px-4 py-3" :style="{ borderBottom: '1px solid var(--border)' }">
                    <svg class="w-5 h-5 shrink-0" :style="{ color: 'var(--text-muted)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input ref="searchInput" v-model="searchQuery" type="text" placeholder="Search orders, customers, products..."
                        class="flex-1 bg-transparent outline-none text-sm" :style="{ color: 'var(--text)' }" />
                    <kbd class="text-[10px] font-semibold px-1.5 py-0.5 rounded" :style="{ background: 'var(--border)', color: 'var(--text-muted)' }">ESC</kbd>
                </div>
                <div class="p-3 max-h-60 overflow-y-auto">
                    <p class="text-center py-6 text-sm" :style="{ color: 'var(--text-muted)' }">Type to search across the application...</p>
                </div>
            </div>
        </div>

        <Toast :show="toast.state.show" :message="toast.state.message" :type="toast.state.type" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Feedback/Toast.vue';
import NotificationBell from '@/Components/Notifications/NotificationBell.vue';
import { useToast } from '@/composables/useToast';
import { useTheme } from '@/composables/useTheme';

const props = defineProps({ user: Object });
const toast = useToast();
const page = usePage();
const showUserMenu = ref(false);
const mobileMenuOpen = ref(false);
const sidebarCollapsed = ref(false);
const showSearch = ref(false);
const searchQuery = ref('');
const searchInput = ref(null);
const { theme, toggleTheme } = useTheme();

const avatarUrl = computed(() => props.user?.avatar ? `/storage/${props.user.avatar}` : null);
const userRole = computed(() => props.user?.roles?.[0]?.replace(/_/g, ' ') || 'User');
const isSalesRep = computed(() => props.user?.roles?.includes('sales_rep'));
const isSuperAdmin = computed(() => props.user?.roles?.includes('super_admin'));

const hasPermission = (permission) => {
    if (isSuperAdmin.value) return true;
    return props.user?.permissions?.includes(permission) || false;
};

const isActive = (routeName) => {
    try {
        const url = new URL(route(routeName), window.location.origin);
        return page.url === url.pathname || (page.url.startsWith(url.pathname + '/') && url.pathname !== '/');
    } catch { return false; }
};

// Ctrl+K
const handleKeydown = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        showSearch.value = !showSearch.value;
        if (showSearch.value) nextTick(() => searchInput.value?.focus());
    }
    if (e.key === 'Escape') {
        showSearch.value = false;
        showUserMenu.value = false;
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    // Apply theme immediately
    const saved = localStorage.getItem('supply4me-theme');
    if (saved) {
        document.documentElement.setAttribute('data-theme', saved);
        if (saved === 'dark') document.documentElement.classList.add('dark');
    }
});

onUnmounted(() => document.removeEventListener('keydown', handleKeydown));

// Close dropdowns on outside click
const handleOutsideClick = (e) => {
    if (!e.target.closest('.relative')) showUserMenu.value = false;
};
onMounted(() => document.addEventListener('click', handleOutsideClick));

const icons = {
    dashboard: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
    customers: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    suppliers: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12l5.12-5.12a2 2 0 012.83 0L12 12m0 0l3.05-3.05a2 2 0 012.83 0L23 12M5 21h14a2 2 0 002-2v-1a7 7 0 00-7-7H7a7 7 0 00-7 7v1a2 2 0 002 2z"/></svg>',
    products: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
    orders: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
    invoices: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>',
    payments: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>',
    stock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
    grn: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    picklist: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
    shipments: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5a1 1 0 01-1 1h-1M1 16h4m-4-5h14"/></svg>',
    deliveries: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
    drivers: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    routes: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
    users: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 4.354a4 4 0 110 7.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
    reports: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
    settings: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
    warehouse: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M3 7v1a3 3 0 006 0V7m0 1a3 3 0 006 0V7m0 1a3 3 0 006 0V7H3l2-4h14l2 4M5 21V10.87M19 21V10.87"/></svg>',
};

const adminNav = computed(() => [
    { label: 'Dashboard', route: 'dashboard', icon: icons.dashboard },
    { label: 'Customers', route: 'customers.index', icon: icons.customers, permission: 'customer.view' },
    { label: 'Suppliers', route: 'suppliers.index', icon: icons.suppliers, permission: 'supplier.view' },
    { label: 'Products', route: 'products.index', icon: icons.products, permission: 'product.view' },
    { label: 'Featured Products', route: 'featured-products.index', icon: icons.products, permission: 'product.view' },
    { label: 'Orders', route: 'orders.index', icon: icons.orders, permission: 'order.view' },
    { label: 'Invoices', route: 'invoices.index', icon: icons.invoices, permission: 'invoice.view' },
    { label: 'Payments', route: 'payments.index', icon: icons.payments, permission: 'payment.view' },
    { label: 'Stock', route: 'stock.index', icon: icons.stock, permission: 'stock.view' },
    { label: 'Warehouses', route: 'warehouses.index', icon: icons.warehouse, permission: 'stock.view' },
].filter(item => !item.permission || hasPermission(item.permission)));

const salesRepNav = [
    { label: 'My Dashboard', route: 'sales-rep.dashboard', icon: icons.dashboard },
    { label: 'My Customers', route: 'sales-rep.customers', icon: icons.customers },
    { label: 'Orders', route: 'orders.index', icon: icons.orders },
];

const mainNav = computed(() => isSalesRep.value ? salesRepNav : adminNav.value);

const adminMoreNavGroups = computed(() => [
    {
        label: 'Operations',
        items: [
            { label: 'GRN', route: 'grn.index', icon: icons.grn, permission: 'grn.view' },
            { label: 'Pick Lists', route: 'pick-lists.index', icon: icons.picklist, permission: 'picklist.view' },
            { label: 'Packing Lists', route: 'packing-lists.index', icon: icons.picklist, permission: 'packinglist.view' },
        ],
    },
    {
        label: 'Shipping',
        items: [
            { label: 'Shipments', route: 'shipments.index', icon: icons.shipments, permission: 'shipment.view' },
            { label: 'Carriers', route: 'shipping-carriers.index', icon: icons.shipments, permission: 'shipment.manage' },
            { label: 'Deliveries', route: 'deliveries.index', icon: icons.deliveries, permission: 'delivery.view' },
            { label: 'Drivers', route: 'drivers.index', icon: icons.drivers, permission: 'delivery.view' },
            { label: 'Delivery Routes', route: 'delivery-routes.index', icon: icons.routes, permission: 'delivery.view-routes' },
        ],
    },
    {
        label: 'Administration',
        items: [
            { label: 'Users', route: 'users.index', icon: icons.users, permission: 'user.view' },
            { label: 'Roles', route: 'roles.index', icon: icons.users, permission: 'role.view' },
            { label: 'Sales Reps', route: 'sales-reps.index', icon: icons.users, permission: 'customer.view' },
            { label: 'Branches', route: 'branches.index', icon: icons.users, permission: 'branch.view' },
        ],
    },
    {
        label: 'Reports',
        items: [
            { label: 'Sales Report', route: 'reports.sales', icon: icons.reports, permission: 'report.view-sales' },
            { label: 'Inventory Report', route: 'reports.inventory', icon: icons.reports, permission: 'report.view-inventory' },
            { label: 'Financial Report', route: 'reports.financial', icon: icons.reports, permission: 'report.view-financial' },
        ],
    },
].map(group => ({
    ...group,
    items: group.items.filter(item => !item.permission || hasPermission(item.permission)),
})).filter(group => group.items.length > 0));

const moreNavGroups = computed(() => isSalesRep.value ? [] : adminMoreNavGroups.value);

const logout = () => router.post(route('logout'));
</script>

<style scoped>
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0.75rem;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
    transition: all 0.18s ease;
    white-space: nowrap;
}
.sidebar-link:hover {
    background: var(--brand-soft);
    color: var(--text);
}
.sidebar-link--active {
    background: var(--brand-soft) !important;
    color: var(--brand) !important;
    font-weight: 600;
}
.sidebar-link--active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 20px;
    border-radius: 0 3px 3px 0;
    background: var(--brand);
}

.dropdown-item {
    display: block;
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
    color: var(--text);
    transition: background 0.12s;
}
.dropdown-item:hover {
    background: var(--brand-soft);
}

/* Transitions */
.fade-enter-active, .fade-leave-active { transition: opacity 0.15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.dropdown-enter-active { transition: all 0.15s ease; }
.dropdown-leave-active { transition: all 0.1s ease; }
.dropdown-enter-from, .dropdown-leave-to { opacity: 0; transform: translateY(-4px); }

.slide-left-enter-active { transition: all 0.25s ease; }
.slide-left-leave-active { transition: all 0.2s ease; }
.slide-left-enter-from { transform: translateX(-100%); }
.slide-left-leave-to { transform: translateX(-100%); }
</style>