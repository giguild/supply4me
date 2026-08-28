<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Payment Information" subtitle="Manage bank account details for customer payments" />

        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4">Bank Account Details</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">This information will be displayed to customers when they make payments.</p>

            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Bank Name</label>
                        <input v-model="form.bank_name" type="text" class="form-input" placeholder="e.g. GTBank, Access Bank, First Bank" :class="{ 'border-red-500': form.errors.bank_name }" />
                        <p v-if="form.errors.bank_name" class="mt-1 text-sm text-red-500">{{ form.errors.bank_name }}</p>
                    </div>

                    <div>
                        <label class="form-label">Account Name</label>
                        <input v-model="form.bank_account_name" type="text" class="form-input" placeholder="e.g. Your Company Limited" :class="{ 'border-red-500': form.errors.bank_account_name }" />
                        <p v-if="form.errors.bank_account_name" class="mt-1 text-sm text-red-500">{{ form.errors.bank_account_name }}</p>
                    </div>

                    <div>
                        <label class="form-label">Account Number</label>
                        <input v-model="form.bank_account_number" type="text" class="form-input" placeholder="e.g. 0123456789" :class="{ 'border-red-500': form.errors.bank_account_number }" />
                        <p v-if="form.errors.bank_account_number" class="mt-1 text-sm text-red-500">{{ form.errors.bank_account_number }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Payment Information' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    company: Object,
});

const toast = useToast();

const form = useForm({
    bank_name: props.company?.bank_name || '',
    bank_account_name: props.company?.bank_account_name || '',
    bank_account_number: props.company?.bank_account_number || '',
});

const submit = () => {
    form.put(route('settings.payment-info.update'), {
        onSuccess: () => {
            toast.success('Payment information updated successfully!');
        },
        onError: () => {
            toast.error('Failed to update payment information. Please check the errors.');
        },
    });
};
</script>
