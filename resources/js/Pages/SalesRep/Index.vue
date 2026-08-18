<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="My Customers" subtitle="Customers assigned to you">
            <template #actions>
                <Link :href="route('sales-rep.dashboard')" class="btn btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </Link>
            </template>
        </PageHeader>

        <!-- Filters -->
        <div class="card rounded-2xl p-4 mb-6">
            <form @submit.prevent="applyFilters" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <input
                        v-model="filters.search"
                        type="text"
                        placeholder="Search customers..."
                        class="form-input"
                    />
                </div>
                <div class="w-full sm:w-48">
                    <select v-model="filters.status" class="form-input">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Customer #</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="customer in customers.data" :key="customer.id">
                            <td class="font-mono text-xs text-gray-600 dark:text-gray-400">{{ customer.customer_number }}</td>
                            <td>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ customer.name }}</p>
                                    <p v-if="customer.trade_name" class="text-xs text-gray-500 dark:text-gray-400">{{ customer.trade_name }}</p>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">
                                    <p v-if="customer.email" class="text-gray-600 dark:text-gray-400">{{ customer.email }}</p>
                                    <p v-if="customer.phone" class="text-gray-600 dark:text-gray-400">{{ customer.phone }}</p>
                                    <p v-if="!customer.email && !customer.phone" class="text-gray-400">-</p>
                                </div>
                            </td>
                            <td class="text-sm text-gray-600 dark:text-gray-400">
                                {{ [customer.city, customer.state].filter(Boolean).join(', ') || '-' }}
                            </td>
                            <td>
                                <StatusBadge :value="customer.status" />
                            </td>
                            <td class="text-sm text-gray-600 dark:text-gray-400">
                                {{ customer.orders_count ?? customer.orders?.length ?? 0 }}
                            </td>
                        </tr>
                        <tr v-if="!customers.data?.length">
                            <td colspan="6">
                                <div class="text-center py-8">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No customers found</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No customers have been assigned to you yet.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="customers.last_page > 1" class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ customers.from }} to {{ customers.to }} of {{ customers.total }} customers
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="page in customers.last_page"
                            :key="page"
                            :href="customers.path + '?page=' + page"
                            class="px-3 py-1 text-sm rounded-lg transition-colors"
                            :class="page === customers.current_page ? 'bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        >
                            {{ page }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
});

const applyFilters = () => {
    router.get(route('sales-rep.customers'), filters, { preserveState: true });
};
</script>
