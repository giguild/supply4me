<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('payments.index')" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
            <PageHeader :title="`Payment ${payment.payment_number}`" />
            <StatusBadge :value="payment.status" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Amount</p>
                <p class="text-lg font-bold text-accent">{{ formatCurrency(payment.amount) }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">{{ payment.payment_type === 'incoming' ? 'Customer' : 'Supplier' }}</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ payment.customer?.name || payment.supplier?.name }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Payment Date</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ payment.payment_date }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Method</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ formatMethod(payment.payment_method) }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Type</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ payment.payment_type === 'incoming' ? 'Incoming' : 'Outgoing' }}</p>
            </div>
            <div v-if="payment.reference_number" class="card p-5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Reference</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ payment.reference_number }}</p>
            </div>
        </div>

        <div v-if="payment.notes" class="card p-6 mb-6">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Notes</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ payment.notes }}</p>
        </div>

        <div v-if="payment.allocations?.length" class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Invoice Allocations</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice#</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="allocation in payment.allocations" :key="allocation.id">
                            <td class="font-medium">
                                <Link :href="route('invoices.show', allocation.invoice_id)" class="text-accent hover:underline">
                                    {{ allocation.invoice?.invoice_number }}
                                </Link>
                            </td>
                            <td class="text-gray-900 dark:text-gray-100">{{ formatCurrency(allocation.amount) }}</td>
                            <td class="text-gray-500 dark:text-gray-400">{{ allocation.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button v-if="payment.status === 'pending'" @click="approvePayment" class="btn" style="background: #d4edda; color: #155724;">
                Approve
            </button>
            <button v-if="payment.status === 'pending'" @click="rejectPayment" class="btn btn-danger">
                Reject
            </button>
        </div>
    </AppLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({ payment: Object });
const toast = useToast();

const approvePayment = () => {
    if (confirm('Are you sure you want to approve this payment?')) {
        router.post(route('payments.approve', props.payment.id), {}, {
            onSuccess: () => toast.success('Payment approved successfully'),
            onError: () => toast.error('Failed to approve payment'),
        });
    }
};

const rejectPayment = () => {
    const reason = prompt('Please enter a reason for rejection:');
    if (reason !== null) {
        router.post(route('payments.reject', props.payment.id), { rejection_reason: reason }, {
            onSuccess: () => toast.success('Payment rejected successfully'),
            onError: () => toast.error('Failed to reject payment'),
        });
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0);
};

const formatMethod = (method) => {
    const methods = {
        cash: 'Cash',
        bank_transfer: 'Bank Transfer',
        credit_card: 'Card',
        check: 'Cheque',
        mobile_money: 'Mobile Money',
        other: 'Other',
    };
    return methods[method] || method;
};
</script>
