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

        <div v-if="receiptUrl" class="card p-6 mb-6">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Payment Receipt</p>
            <div v-if="isImage" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-800">
                <img :src="receiptUrl" alt="Payment receipt" class="max-w-full max-h-96 mx-auto cursor-pointer hover:opacity-90 transition-opacity" @click="showFullReceipt = true" />
            </div>
            <div v-else-if="isPdf" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <iframe :src="receiptUrl" class="w-full h-96" frameborder="0"></iframe>
            </div>
            <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                <a :href="receiptUrl" target="_blank" class="text-accent hover:underline">Download receipt</a>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="showFullReceipt" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70" @click.self="showFullReceipt = false">
                <div class="relative max-w-4xl w-full mx-4">
                    <button @click="showFullReceipt = false" class="absolute -top-10 right-0 text-white hover:text-gray-300">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                    <img :src="receiptUrl" alt="Payment receipt full size" class="max-w-full max-h-[85vh] mx-auto rounded-lg" />
                </div>
            </div>
        </Teleport>

        <div v-if="payment.allocations?.length" class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Invoice Allocation</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice#</th>
                            <th>Invoice Total</th>
                            <th>Paid</th>
                            <th>Outstanding</th>
                            <th>Status</th>
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
                            <td class="text-gray-900 dark:text-gray-100">{{ formatCurrency(allocation.invoice?.total_amount) }}</td>
                            <td class="text-green-600 dark:text-green-400 font-medium">{{ formatCurrency(allocation.invoice?.paid_amount) }}</td>
                            <td class="text-red-600 dark:text-red-400 font-medium">{{ formatCurrency(allocation.invoice?.due_amount) }}</td>
                            <td>
                                <span :class="{
                                    'badge badge-success': allocation.invoice?.status === 'paid',
                                    'badge badge-warning': allocation.invoice?.status === 'partial',
                                    'badge badge-danger': allocation.invoice?.status === 'overdue',
                                    'badge badge-info': !['paid','partial','overdue'].includes(allocation.invoice?.status),
                                }">
                                    {{ allocation.invoice?.status }}
                                </span>
                            </td>
                            <td class="text-gray-500 dark:text-gray-400">{{ allocation.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="payment.notes" class="card p-6 mb-6">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Notes</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ payment.notes }}</p>
        </div>

        <div v-if="payment.status === 'pending'" class="card p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-4">Review Payment</h3>
            <div class="flex flex-wrap gap-3">
                <button @click="markPaid" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors" style="background: #15803d;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Mark as Paid ({{ formatCurrency(payment.amount) }})
                </button>
                <button @click="showPartialModal = true" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold border border-amber-400 text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Partial Payment
                </button>
                <button @click="rejectPayment" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold border border-red-300 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    Reject
                </button>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="showPartialModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showPartialModal = false">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Record Partial Payment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Enter the amount the customer has paid so far.</p>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Paid</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">&#8358;</span>
                            <input v-model="partialAmount" type="number" step="0.01" :max="payment.amount" min="0.01"
                                class="form-input pl-8 w-full" placeholder="0.00" />
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Payment amount: {{ formatCurrency(payment.amount) }}</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button @click="showPartialModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">Cancel</button>
                        <button @click="submitPartial" :disabled="!partialAmount || partialAmount <= 0" class="px-4 py-2 text-sm font-semibold text-white rounded-lg transition-colors disabled:opacity-50" style="background: #9f5124;">
                            Confirm Partial Payment
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({ payment: Object });
const toast = useToast();
const showFullReceipt = ref(false);
const showPartialModal = ref(false);
const partialAmount = ref(null);

const receiptPath = computed(() => {
    if (!props.payment.metadata) return null;
    const meta = typeof props.payment.metadata === 'string' ? JSON.parse(props.payment.metadata) : props.payment.metadata;
    return meta?.receipt_path || null;
});

const receiptUrl = computed(() => receiptPath.value ? `/storage/${receiptPath.value}` : null);

const isImage = computed(() => {
    if (!receiptUrl.value) return false;
    return /\.(jpg|jpeg|png|gif|webp)$/i.test(receiptUrl.value);
});

const isPdf = computed(() => {
    if (!receiptUrl.value) return false;
    return /\.pdf$/i.test(receiptUrl.value);
});

const markPaid = () => {
    if (confirm('Mark this payment as fully paid? The linked invoice will be updated.')) {
        router.post(route('payments.approve', props.payment.id), {}, {
            onSuccess: () => toast.success('Payment approved and invoice updated'),
            onError: () => toast.error('Failed to approve payment'),
        });
    }
};

const submitPartial = () => {
    if (!partialAmount.value || partialAmount.value <= 0) return;
    showPartialModal.value = false;
    router.post(route('payments.markPartial', props.payment.id), { paid_amount: partialAmount.value }, {
        onSuccess: () => toast.success('Partial payment recorded and invoice updated'),
        onError: () => toast.error('Failed to record partial payment'),
    });
};

const rejectPayment = () => {
    const reason = prompt('Please enter a reason for rejection:');
    if (reason !== null) {
        router.post(route('payments.reject', props.payment.id), { rejection_reason: reason }, {
            onSuccess: () => toast.success('Payment rejected'),
            onError: () => toast.error('Failed to reject payment'),
        });
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount || 0);
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
