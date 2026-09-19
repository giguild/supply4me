<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="expense.description" subtitle="Expense Details">
            <template #actions>
                <div class="flex gap-2">
                    <Link :href="route('expenses.edit', expense)" class="btn btn-outline">Edit</Link>
                    <button @click="confirmDelete" class="btn btn-danger">Delete</button>
                </div>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card rounded-2xl p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Amount</p>
                        <p class="text-2xl font-bold text-[#9F5124]">₦ {{ formatNumber(expense.amount) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        <StatusBadge :value="expense.status" :variant="expense.status === 'approved' ? 'success' : expense.status === 'rejected' ? 'danger' : 'warning'" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Date</p>
                        <p class="font-medium">{{ formatDate(expense.expense_date) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Category</p>
                        <p class="font-medium">{{ expense.category?.name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                        <p class="font-medium capitalize">{{ expense.payment_method?.replace('_', ' ') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Vendor</p>
                        <p class="font-medium">{{ expense.vendor ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Reference</p>
                        <p class="font-medium">{{ expense.reference ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Recorded By</p>
                        <p class="font-medium">{{ expense.creator?.name ?? '-' }}</p>
                    </div>
                </div>

                <div v-if="expense.notes" class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-sm text-gray-500 mb-1">Notes</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ expense.notes }}</p>
                </div>
            </div>

            <div class="card rounded-2xl p-6">
                <h3 class="font-semibold mb-4">Receipt</h3>
                <div v-if="expense.receipt_path">
                    <a :href="'/storage/' + expense.receipt_path" target="_blank" class="text-[#9F5124] hover:underline text-sm">View Receipt</a>
                </div>
                <p v-else class="text-sm text-gray-500">No receipt uploaded</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

const props = defineProps({
    expense: Object,
});

const formatDate = (d) => new Date(d).toLocaleDateString('en-NG', { day: 'numeric', month: 'long', year: 'numeric' });
const formatNumber = (v) => Number(v).toLocaleString('en-NG', { maximumFractionDigits: 2 });

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this expense?')) {
        router.delete(route('expenses.destroy', props.expense));
    }
};
</script>
