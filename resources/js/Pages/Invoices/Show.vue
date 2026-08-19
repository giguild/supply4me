<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('invoices.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
            <PageHeader :title="`Invoice ${invoice.invoice_number}`" />
            <StatusBadge :value="invoice.status" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Customer</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ invoice.customer?.name }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Invoice Date</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ invoice.invoice_date }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Due Date</p>
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ invoice.due_date }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Balance Due</p>
                <p class="text-sm font-bold text-red-600">{{ formatCurrency(invoice.due_amount) }}</p>
            </div>
        </div>

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Invoice Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Tax</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!invoice.items || invoice.items.length === 0">
                            <td colspan="6">
                                <div class="text-center py-8">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" /></svg>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No items</p>
                                    <p class="text-sm text-gray-500">This invoice has no items yet.</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="item in invoice.items" :key="item.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ item.name || item.description }}</td>
                            <td class="text-gray-500">{{ item.quantity }}</td>
                            <td class="text-gray-500">{{ formatCurrency(item.unit_price) }}</td>
                            <td class="text-gray-500">{{ formatCurrency(item.tax_amount) }}</td>
                            <td class="text-gray-500">{{ formatCurrency(item.discount_amount) }}</td>
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(item.line_total) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end mb-6">
            <div class="card p-6 w-80 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(invoice.subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Tax</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(invoice.tax_amount) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Discount</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">-{{ formatCurrency(invoice.discount_amount) }}</span>
                </div>
                <div class="border-t pt-3 flex justify-between">
                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total</span>
                    <span class="text-lg font-bold text-accent">{{ formatCurrency(invoice.total_amount) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Paid</span>
                    <span class="font-medium text-green-600">{{ formatCurrency(invoice.paid_amount) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Balance Due</span>
                    <span class="font-medium text-red-600">{{ formatCurrency(invoice.due_amount) }}</span>
                </div>
            </div>
        </div>

        <div v-if="invoice.notes" class="card p-6 mb-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Notes</p>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ invoice.notes }}</p>
        </div>

        <div v-if="invoice.payments?.length" class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Payment History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Payment#</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Receipt</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="pmt in invoice.payments" :key="pmt.id">
                            <td class="font-medium">
                                <Link :href="route('payments.show', pmt.id)" class="text-accent hover:underline">
                                    {{ pmt.payment_number }}
                                </Link>
                            </td>
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(pmt.amount) }}</td>
                            <td class="text-gray-500">{{ formatMethod(pmt.payment_method) }}</td>
                            <td><StatusBadge :value="pmt.status" /></td>
                            <td>
                                <a v-if="getReceiptPath(pmt)" :href="`/storage/${getReceiptPath(pmt)}`" target="_blank" class="text-accent hover:underline text-sm">View</a>
                                <span v-else class="text-gray-400 text-sm">-</span>
                            </td>
                            <td class="text-gray-500">{{ pmt.payment_date }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button v-if="invoice.status === 'draft'" @click="sendInvoice" class="btn" style="background: #d4edda; color: #155724;">
                Send Invoice
            </button>
            <Link v-if="invoice.status === 'draft'" :href="route('invoices.edit', invoice.id)" class="btn btn-outline">
                Edit
            </Link>
            <button v-if="invoice.status !== 'void' && invoice.status !== 'paid' && invoice.due_amount > 0" @click="showPaymentModal = true" class="btn btn-accent">
                Record Payment
            </button>
            <button v-if="invoice.status !== 'void' && invoice.status !== 'paid'" @click="voidInvoice" class="btn btn-danger">
                Void
            </button>
        </div>

        <Teleport to="body">
            <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showPaymentModal = false">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Record Payment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Record a payment against {{ invoice.invoice_number }}. Outstanding: {{ formatCurrency(invoice.due_amount) }}</p>

                    <form @submit.prevent="submitPayment">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount (₦)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium">₦</span>
                                    <input v-model.number="paymentAmount" type="number" step="0.01" min="0.01" :max="invoice.due_amount" required
                                        class="form-input pl-8 w-full" placeholder="0.00" />
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Max: {{ formatCurrency(invoice.due_amount) }}</p>
                                <p v-if="paymentAmount > invoice.due_amount" class="text-xs text-red-500 mt-1">Amount exceeds outstanding balance</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Method</label>
                                <select v-model="paymentMethod" required class="form-input w-full">
                                    <option value="">Select method</option>
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="credit_card">Card</option>
                                    <option value="check">Cheque</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Payment Date</label>
                                <input v-model="paymentDate" type="date" required class="form-input w-full" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reference Number</label>
                                <input v-model="paymentRef" type="text" class="form-input w-full" placeholder="e.g. transfer ref, cheque no." />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                                <textarea v-model="paymentNotes" rows="2" class="form-input w-full" placeholder="Optional notes"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showPaymentModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">Cancel</button>
                            <button type="submit" :disabled="!paymentAmount || paymentAmount <= 0 || paymentAmount > invoice.due_amount || !paymentMethod" class="btn btn-accent disabled:opacity-50">
                                Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({ invoice: Object });
const toast = useToast();
const showPaymentModal = ref(false);

const today = new Date().toISOString().split('T')[0];
const paymentAmount = ref(props.invoice.due_amount || 0);
const paymentMethod = ref('');
const paymentDate = ref(today);
const paymentRef = ref('');
const paymentNotes = ref('');

const submitPayment = () => {
    router.post(route('invoices.payments.store', props.invoice.id), {
        amount: Number(paymentAmount.value),
        payment_method: paymentMethod.value,
        payment_date: paymentDate.value,
        reference_number: paymentRef.value,
        notes: paymentNotes.value,
    }, {
        onSuccess: () => {
            showPaymentModal.value = false;
            paymentAmount.value = props.invoice.due_amount || 0;
            paymentMethod.value = '';
            paymentDate.value = today;
            paymentRef.value = '';
            paymentNotes.value = '';
            toast.success('Payment recorded successfully');
        },
        onError: (errors) => {
            const msg = errors.amount || errors.payment_method || 'Failed to record payment';
            toast.error(msg);
        },
    });
};

const sendInvoice = () => {
    if (confirm('Are you sure you want to send this invoice?')) {
        router.post(route('invoices.send', props.invoice.id), {}, {
            onSuccess: () => toast.success('Invoice sent successfully'),
            onError: () => toast.error('Failed to send invoice'),
        });
    }
};

const voidInvoice = () => {
    if (confirm('Are you sure you want to void this invoice? This action cannot be undone.')) {
        router.post(route('invoices.void', props.invoice.id), {}, {
            onSuccess: () => toast.success('Invoice voided successfully'),
            onError: () => toast.error('Failed to void invoice'),
        });
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount || 0);
};

const formatMethod = (method) => {
    const methods = { cash: 'Cash', bank_transfer: 'Bank Transfer', credit_card: 'Card', check: 'Cheque', mobile_money: 'Mobile Money', other: 'Other' };
    return methods[method] || method;
};

const getReceiptPath = (pmt) => {
    if (!pmt.metadata) return null;
    const meta = typeof pmt.metadata === 'string' ? JSON.parse(pmt.metadata) : pmt.metadata;
    return meta?.receipt_path || null;
};
</script>
