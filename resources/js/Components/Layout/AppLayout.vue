<template>
    <div class="min-h-screen" :class="theme === 'dark' ? 'bg-gray-900' : ''" style="background: linear-gradient(135deg, #f5f0eb 0%, #faf8f6 50%, #f5f0eb 100%)" :style="theme === 'dark' ? { background: 'linear-gradient(135deg, #111827 0%, #1a1f2e 50%, #111827 100%)' } : {}">
        <!-- Top Header Bar -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Left: Logo + Nav -->
                    <div class="flex items-center gap-6 min-w-0">
                        <Link :href="route('dashboard')" class="flex items-center gap-2 shrink-0">
                            <img v-if="theme === 'dark'" src="/images/logo_light.png" alt="SUPPLY4ME" class="h-8 w-auto" />
                            <img v-else src="/images/logo_dark.png" alt="SUPPLY4ME" class="h-8 w-auto" />
                            <span class="font-bold text-gray-900 dark:text-gray-100 hidden sm:block">SUPPLY4ME</span>
                        </Link>

                        <!-- Desktop Nav -->
                        <nav class="hidden lg:flex items-center gap-0.5 overflow-x-auto">
                            <template v-for="item in mainNav" :key="item.route">
                                <Link :href="route(item.route)"
                                    class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap"
                                    :class="isActive(item.route) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    {{ item.label }}
                                </Link>
                            </template>

                            <div class="w-px h-5 bg-gray-200 dark:bg-gray-700 mx-1 shrink-0"></div>

                            <template v-for="group in moreNavGroups" :key="group.label">
                                <div class="px-2 py-1 text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider whitespace-nowrap shrink-0">{{ group.label }}</div>
                                <Link v-for="item in group.items" :key="item.route" :href="route(item.route)"
                                    class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap"
                                    :class="isActive(item.route) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'">
                                    {{ item.label }}
                                </Link>
                                <div class="w-px h-5 bg-gray-200 dark:bg-gray-700 mx-1 shrink-0"></div>
                            </template>

                            <Link :href="route('settings.index')"
                                class="px-2.5 py-1.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap"
                                :class="isActive('settings.index') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'">
                                Settings
                            </Link>
                        </nav>
                    </div>

                    <!-- Right: Theme Toggle + Search + User -->
                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Theme Toggle -->
                        <button @click="toggleTheme" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" :title="theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'">
                            <svg v-if="theme === 'dark'" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <svg v-else class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        </button>

                        <!-- Search -->
                        <div class="hidden md:flex items-center bg-gray-50 dark:bg-gray-700 rounded-lg px-3 py-1.5 w-48">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Search..." class="bg-transparent text-sm outline-none w-full text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500" />
                        </div>

                        <!-- Notifications -->
                        <button class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>

                        <!-- User -->
                        <div class="flex items-center gap-2 pl-2 border-l border-gray-200 dark:border-gray-700">
                            <div class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ user?.name?.charAt(0) || 'U' }}
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-tight">{{ user?.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight">{{ userRole }}</p>
                            </div>
                            <button @click="showUserMenu = !showUserMenu" class="p-1">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>

                        <!-- User Dropdown -->
                        <div v-if="showUserMenu" class="absolute right-4 top-14 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Profile</Link>
                            <Link :href="route('settings.index')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Settings</Link>
                            <hr class="my-1 border-gray-200 dark:border-gray-700" />
                            <button @click="logout" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">Logout</button>
                        </div>

                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Nav -->
            <div v-if="mobileMenuOpen" class="lg:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 max-h-[70vh] overflow-y-auto">
                <div class="px-4 py-3 space-y-1">
                    <Link v-for="item in mainNav" :key="item.route" :href="route(item.route)"
                        @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium"
                        :class="isActive(item.route) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'">
                        <span v-html="item.icon" class="w-4 h-4" />
                        {{ item.label }}
                    </Link>

                    <hr class="my-2 border-gray-200 dark:border-gray-700" />

                    <template v-for="group in moreNavGroups" :key="group.label">
                        <div class="px-3 py-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ group.label }}</div>
                        <Link v-for="item in group.items" :key="item.route" :href="route(item.route)"
                            @click="mobileMenuOpen = false"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium"
                            :class="isActive(item.route) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'">
                            <span v-html="item.icon" class="w-4 h-4" />
                            {{ item.label }}
                        </Link>
                    </template>

                    <hr class="my-2 border-gray-200 dark:border-gray-700" />

                    <Link :href="route('settings.index')" @click="mobileMenuOpen = false"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <span v-html="icons.settings" class="w-4 h-4" />
                        Settings
                    </Link>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <slot />
        </main>

        <Toast :show="toast.state.show" :message="toast.state.message" :type="toast.state.type" />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Toast from '@/Components/Feedback/Toast.vue';
import { useToast } from '@/composables/useToast';
import { useTheme } from '@/composables/useTheme';

const props = defineProps({ user: Object });
const toast = useToast();
const page = usePage();
const showUserMenu = ref(false);
const mobileMenuOpen = ref(false);
const { theme, toggleTheme } = useTheme();

const userRole = computed(() => props.user?.roles?.[0]?.name || 'User');

const isActive = (routeName) => {
    try {
        const url = new URL(route(routeName), window.location.origin);
        return page.url === url.pathname || (page.url.startsWith(url.pathname + '/') && url.pathname !== '/');
    } catch { return false; }
};

const icons = {
    dashboard: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    customers: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    suppliers: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12l5.12-5.12a2 2 0 012.83 0L12 12m0 0l3.05-3.05a2 2 0 012.83 0L23 12M5 21h14a2 2 0 002-2v-1a7 7 0 00-7-7H7a7 7 0 00-7 7v1a2 2 0 002 2z"/></svg>',
    products: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
    orders: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
    invoices: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>',
    payments: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>',
    stock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
    grn: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    picklist: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
    shipments: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5a1 1 0 01-1 1h-1M1 16h4m-4-5h14"/></svg>',
    deliveries: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>',
    drivers: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>',
    routes: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
    users: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4.354a4 4 0 110 7.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
    reports: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
    settings: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
};

const mainNav = [
    { label: 'Dashboard', route: 'dashboard', icon: icons.dashboard },
    { label: 'Customers', route: 'customers.index', icon: icons.customers },
    { label: 'Suppliers', route: 'suppliers.index', icon: icons.suppliers },
    { label: 'Products', route: 'products.index', icon: icons.products },
    { label: 'Orders', route: 'orders.index', icon: icons.orders },
    { label: 'Invoices', route: 'invoices.index', icon: icons.invoices },
    { label: 'Payments', route: 'payments.index', icon: icons.payments },
    { label: 'Stock', route: 'stock.index', icon: icons.stock },
];

const moreNavGroups = [
    {
        label: 'Receiving',
        items: [
            { label: 'GRN', route: 'grn.index', icon: icons.grn },
            { label: 'Pick Lists', route: 'pick-lists.index', icon: icons.picklist },
            { label: 'Packing Lists', route: 'packing-lists.index', icon: icons.picklist },
        ],
    },
    {
        label: 'Shipping',
        items: [
            { label: 'Shipments', route: 'shipments.index', icon: icons.shipments },
            { label: 'Deliveries', route: 'deliveries.index', icon: icons.deliveries },
            { label: 'Drivers', route: 'drivers.index', icon: icons.drivers },
            { label: 'Delivery Routes', route: 'delivery-routes.index', icon: icons.routes },
        ],
    },
    {
        label: 'Administration',
        items: [
            { label: 'Users', route: 'users.index', icon: icons.users },
            { label: 'Branches', route: 'branches.index', icon: icons.users },
        ],
    },
    {
        label: 'Reports',
        items: [
            { label: 'Sales Report', route: 'reports.sales', icon: icons.reports },
            { label: 'Inventory Report', route: 'reports.inventory', icon: icons.reports },
            { label: 'Financial Report', route: 'reports.financial', icon: icons.reports },
        ],
    },
];

const logout = () => router.post(route('logout'));
</script>
