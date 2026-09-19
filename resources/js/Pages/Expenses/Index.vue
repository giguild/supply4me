<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Expenses" subtitle="Track and manage business expenses">
            <template #actions>
                <Link :href="route('expenses.create')" class="btn btn-accent">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Expense
                </Link>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <StatCard label="Total Expenses" :value="summary.total" prefix="₦ " subtitle="all time" />
            <StatCard label="Approved" :value="summary.approved" prefix="₦ " subtitle="approved expenses" />
            <StatCard label="Pending" :value="summary.pending" prefix="₦ " subtitle="awaiting approval" />
        </div>

        <div class="card rounded-2xl p-4 mb-6">
            <form @submit.prevent="applyFilters" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1">
                    <label class="form-label">Search</label>
                    <input v-model="filters.search" type="text" class="form-input" placeholder="Description, vendor..." />
                </div>
                <div class="w-full sm:w-44">
                    <label class="form-label">Category</label>
                    <select v-model="filters.category_id" class="form-input">
                        <option value="">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
                <div class="w-full sm:w-36">
                    <label class="form-label">Status</label>
                    <select v-model="filters.status" class="form-input">
                        <option value="">All</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label class="form-label">From</label>
                    <input v-model="filters.start_date" type="date" class="form-input" />
                </div>
                <div class="w-full sm:w-40">
                    <label class="form-label">To</label>
                    <input v-model="filters.end_date" type="date" class="form-input" />
                </div>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Vendor</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="expense in expenses.data" :key="expense.id">
                            <td class="whitespace-nowrap">{{ formatDate(expense.expense_date) }}</td>
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ expense.description }}</td>
                            <td class="text-gray-600 dark:text-gray-400">{{ expense.category?.name ?? '-' }}</td>
                            <td class="text-gray-600 dark:text-gray-400">{{ expense.vendor ?? '-' }}</td>
                            <td class="font-medium">₦ {{ formatNumber(expense.amount) }}</td>
                            <td>
                                <StatusBadge :value="expense.status" :variant="expense.status === 'approved' ? 'success' : expense.status === 'rejected' ? 'danger' : 'warning'" />
                            </td>
                            <td class="text-right">
                                <Link :href="route('expenses.show', expense)" class="text-sm text-[#9F5124] hover:underline">View</Link>
                            </td>
                        </tr>
                        <tr v-if="!expenses.data?.length">
                            <td colspan="7" class="text-center py-8 text-gray-500">No expenses found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-if="expenses.last_page > 1" class="flex items-center justify-between px-6 py-3 border-t border-gray-100 dark:border-gray-700">
                <span class="text-sm text-gray-500">Showing {{ expenses.from }}-{{ expenses.to }} of {{ expenses.total }}</span>
                <div class="flex gap-1">
                    <Link v-for="p in expenses.last_page" :key="p" :href="expenses.path + '?page=' + p"
                        :class="['px-3 py-1 rounded-lg text-sm', p === expenses.current_page ? 'bg-gray-900 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700']">
                        {{ p }}
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

const props = defineProps({
    expenses: Object,
    categories: Array,
    summary: Object,
    filters: Object,
});

const filters = reactive({
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    category_id: props.filters?.category_id || '',
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
});

const formatDate = (d) => new Date(d).toLocaleDateString('en-NG', { day: 'numeric', month: 'short', year: 'numeric' });
const formatNumber = (v) => Number(v).toLocaleString('en-NG', { maximumFractionDigits: 2 });

const applyFilters = () => {
    router.get(route('expenses.index'), filters, { preserveState: true });
};
</script>
