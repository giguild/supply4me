<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="`Edit Invoice ${invoice.invoice_number}`" />

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="card p-6">
                    <label class="form-label">Customer *</label>
                    <select v-model="form.customer_id" class="form-input" required>
                        <option value="">Select Customer</option>
                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                            {{ customer.name }}
                        </option>
                    </select>
                    <p v-if="form.errors.customer_id" class="text-red-500 text-xs mt-1">{{ form.errors.customer_id }}</p>
                </div>
                <div class="card p-6">
                    <label class="form-label">Order (Optional)</label>
                    <select v-model="form.order_id" class="form-input">
                        <option value="">No Linked Order</option>
                        <option v-for="order in orders" :key="order.id" :value="order.id">
                            {{ order.order_number }}
                        </option>
                    </select>
                </div>
                <div class="card p-6">
                    <label class="form-label">Invoice Date *</label>
                    <input v-model="form.invoice_date" type="date" class="form-input" required />
                </div>
                <div class="card p-6">
                    <label class="form-label">Due Date *</label>
                    <input v-model="form.due_date" type="date" class="form-input" required />
                </div>
            </div>

            <div class="card p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Invoice Items</h3>
                    <button type="button" @click="addItem" class="btn btn-accent btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, index) in form.items" :key="index">
                                <td>
                                    <input v-model="item.description" type="text" class="form-input" placeholder="Item description" required />
                                </td>
                                <td>
                                    <input v-model.number="item.quantity" type="number" min="1" step="1" class="form-input w-20" required />
                                </td>
                                <td>
                                    <input v-model.number="item.unit_price" type="number" min="0" step="0.01" class="form-input w-28" required />
                                </td>
                                <td class="text-sm font-medium text-gray-900">
                                    {{ formatCurrency(item.quantity * item.unit_price) }}
                                </td>
                                <td>
                                    <button v-if="form.items.length > 1" type="button" @click="removeItem(index)" class="text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end mb-6">
                <div class="card p-6 w-80 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-900">{{ formatCurrency(form.subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Tax</span>
                        <input v-model.number="form.tax_amount" type="number" min="0" step="0.01" class="form-input w-24 text-right" />
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Discount</span>
                        <input v-model.number="form.discount_amount" type="number" min="0" step="0.01" class="form-input w-24 text-right" />
                    </div>
                    <div class="border-t pt-3 flex justify-between">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-accent">{{ formatCurrency(form.total_amount) }}</span>
                    </div>
                </div>
            </div>

            <div class="card p-6 mb-6">
                <label class="form-label">Notes</label>
                <textarea v-model="form.notes" rows="3" class="form-input" placeholder="Optional notes..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <Link :href="route('invoices.show', invoice.id)" class="btn btn-outline">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn btn-accent">
                    {{ form.processing ? 'Updating...' : 'Update Invoice' }}
                </button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import { watch } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    invoice: Object,
    customers: Array,
    orders: Array,
});

const toast = useToast();

const form = useForm({
    customer_id: props.invoice.customer_id,
    order_id: props.invoice.order_id || '',
    invoice_date: props.invoice.invoice_date,
    due_date: props.invoice.due_date,
    notes: props.invoice.notes || '',
    items: props.invoice.items.map(item => ({
        description: item.description || item.name,
        quantity: parseFloat(item.quantity),
        unit_price: parseFloat(item.unit_price),
    })),
    subtotal: parseFloat(props.invoice.subtotal),
    tax_amount: parseFloat(props.invoice.tax_amount),
    discount_amount: parseFloat(props.invoice.discount_amount),
    total_amount: parseFloat(props.invoice.total_amount),
});

const addItem = () => {
    form.items.push({ description: '', quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const recalculateTotals = () => {
    let subtotal = 0;
    form.items.forEach(item => {
        subtotal += item.quantity * item.unit_price;
    });
    form.subtotal = subtotal;
    form.total_amount = subtotal + form.tax_amount - form.discount_amount;
};

watch(() => form.items, recalculateTotals, { deep: true });
watch(() => form.tax_amount, recalculateTotals);
watch(() => form.discount_amount, recalculateTotals);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount || 0);
};

const submit = () => {
    recalculateTotals();
    form.put(route('invoices.update', props.invoice.id), {
        onSuccess: () => toast.success('Invoice updated successfully'),
        onError: () => toast.error('Failed to update invoice'),
    });
};
</script>
