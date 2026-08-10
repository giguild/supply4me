<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Create Payment" />

        <form @submit.prevent="submit">
            <div class="card p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Payment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Payment Type *</label>
                        <div class="flex gap-2">
                            <button type="button" @click="form.payment_type = 'incoming'"
                                :class="['flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all border-2',
                                    form.payment_type === 'incoming' ? 'border-[#9f5124] bg-[#9f5124]/5 text-[#9f5124]' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300']">
                                Customer Payment
                            </button>
                            <button type="button" @click="form.payment_type = 'outgoing'"
                                :class="['flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all border-2',
                                    form.payment_type === 'outgoing' ? 'border-[#9f5124] bg-[#9f5124]/5 text-[#9f5124]' : 'border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:border-gray-300']">
                                Supplier Payment
                            </button>
                        </div>
                    </div>

                    <div v-if="form.payment_type === 'incoming'">
                        <label class="form-label">Customer *</label>
                        <select v-model="form.customer_id" class="form-input" :required="form.payment_type === 'incoming'">
                            <option value="">Select Customer</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.customer_id" class="text-red-500 text-xs mt-1">{{ form.errors.customer_id }}</p>
                    </div>

                    <div v-if="form.payment_type === 'outgoing'">
                        <label class="form-label">Supplier *</label>
                        <select v-model="form.supplier_id" class="form-input" :required="form.payment_type === 'outgoing'">
                            <option value="">Select Supplier</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.supplier_id" class="text-red-500 text-xs mt-1">{{ form.errors.supplier_id }}</p>
                    </div>

                    <div>
                        <label class="form-label">Amount *</label>
                        <input v-model.number="form.amount" type="number" min="0.01" step="0.01" class="form-input" required />
                    </div>

                    <div>
                        <label class="form-label">Payment Method *</label>
                        <select v-model="form.payment_method" class="form-input" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Cheque</option>
                            <option value="credit_card">Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Payment Date *</label>
                        <input v-model="form.payment_date" type="date" class="form-input" required />
                    </div>

                    <div>
                        <label class="form-label">Reference Number</label>
                        <input v-model="form.reference_number" type="text" class="form-input" placeholder="Transaction reference" />
                    </div>
                </div>
            </div>

            <div class="card p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Invoice Allocation (Optional)</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select invoices to allocate this payment to.</p>
                <div v-if="filteredInvoices.length > 0" class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="w-10"><input type="checkbox" @change="toggleAllInvoices" class="rounded" /></th>
                                <th>Invoice#</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Balance Due</th>
                                <th>Allocate Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="invoice in filteredInvoices" :key="invoice.id">
                                <td><input type="checkbox" :value="invoice.id" v-model="selectedInvoices" class="rounded" /></td>
                                <td class="font-medium text-gray-900 dark:text-gray-100">{{ invoice.invoice_number }}</td>
                                <td class="text-gray-500 dark:text-gray-400">{{ invoice.invoice_date }}</td>
                                <td class="text-gray-500 dark:text-gray-400">{{ formatCurrency(invoice.total_amount) }}</td>
                                <td class="text-red-600">{{ formatCurrency(invoice.due_amount) }}</td>
                                <td>
                                    <input v-if="selectedInvoices.includes(invoice.id)" v-model.number="allocations[invoice.id]"
                                        type="number" min="0.01" :max="invoice.due_amount" step="0.01" class="form-input w-28" placeholder="0.00" />
                                    <span v-else class="text-sm text-gray-400 dark:text-gray-500">-</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="text-sm text-gray-400 dark:text-gray-500">No outstanding invoices for this customer/supplier.</p>
            </div>

            <div class="flex justify-end gap-3">
                <Link :href="route('payments.index')" class="btn btn-outline">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn btn-accent">
                    {{ form.processing ? 'Creating...' : 'Create Payment' }}
                </button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    customers: Array,
    suppliers: Array,
    invoices: Array,
});

const toast = useToast();
const selectedInvoices = ref([]);
const allocations = ref({});

const form = useForm({
    payment_type: 'incoming',
    customer_id: '',
    supplier_id: '',
    amount: 0,
    payment_method: 'cash',
    payment_date: new Date().toISOString().split('T')[0],
    reference_number: '',
    notes: '',
    allocations: [],
});

const filteredInvoices = computed(() => {
    if (form.payment_type === 'incoming' && form.customer_id) {
        return props.invoices.filter(inv => inv.customer_id === form.customer_id && inv.due_amount > 0);
    }
    return [];
});

const toggleAllInvoices = (e) => {
    if (e.target.checked) {
        selectedInvoices.value = filteredInvoices.value.map(inv => inv.id);
    } else {
        selectedInvoices.value = [];
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0);
};

const submit = () => {
    form.allocations = selectedInvoices.value
        .filter(id => allocations.value[id] > 0)
        .map(id => ({ invoice_id: id, amount: allocations.value[id] }));

    form.post(route('payments.store'), {
        onSuccess: () => toast.success('Payment created successfully'),
        onError: () => toast.error('Failed to create payment'),
    });
};
</script>
