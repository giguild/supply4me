<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Edit Expense" subtitle="Update expense details" />

        <form @submit.prevent="submit" class="max-w-2xl">
            <div class="card rounded-2xl p-6 space-y-5">
                <div>
                    <label class="form-label">Description <span class="text-red-500">*</span></label>
                    <input v-model="form.description" type="text" class="form-input" required />
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Amount (₦) <span class="text-red-500">*</span></label>
                        <input v-model="form.amount" type="number" step="0.01" min="0.01" class="form-input" required />
                    </div>
                    <div>
                        <label class="form-label">Date <span class="text-red-500">*</span></label>
                        <input v-model="form.expense_date" type="date" class="form-input" required />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Category</label>
                        <select v-model="form.expense_category_id" class="form-input">
                            <option value="">Select category</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Payment Method</label>
                        <select v-model="form.payment_method" class="form-input">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="mobile">Mobile Payment</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="form-label">Vendor</label>
                        <input v-model="form.vendor" type="text" class="form-input" />
                    </div>
                    <div>
                        <label class="form-label">Reference</label>
                        <input v-model="form.reference" type="text" class="form-input" />
                    </div>
                </div>

                <div>
                    <label class="form-label">Status</label>
                    <select v-model="form.status" class="form-input">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Notes</label>
                    <textarea v-model="form.notes" rows="3" class="form-input"></textarea>
                </div>

                <div>
                    <label class="form-label">Replace Receipt</label>
                    <input type="file" @change="handleReceipt" accept="image/*,.pdf" class="form-input" />
                    <p v-if="expense.receipt_path" class="mt-1 text-xs text-gray-500">Current receipt exists. Upload new to replace.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <Link :href="route('expenses.show', expense)" class="btn btn-outline">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn btn-accent">
                    {{ form.processing ? 'Saving...' : 'Update Expense' }}
                </button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';

const props = defineProps({
    expense: Object,
    categories: Array,
});

const form = useForm({
    description: props.expense.description,
    amount: props.expense.amount,
    expense_date: props.expense.expense_date?.split('T')[0],
    expense_category_id: props.expense.expense_category_id ?? '',
    payment_method: props.expense.payment_method,
    vendor: props.expense.vendor ?? '',
    reference: props.expense.reference ?? '',
    notes: props.expense.notes ?? '',
    status: props.expense.status,
    receipt: null,
});

const handleReceipt = (e) => {
    form.receipt = e.target.files[0];
};

const submit = () => {
    form.put(route('expenses.update', props.expense), {
        forceFormData: true,
    });
};
</script>
