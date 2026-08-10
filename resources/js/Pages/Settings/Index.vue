<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Settings" subtitle="Manage company information and preferences" />

        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-semibold mb-4">Company Information</h3>

            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Company Name</label>
                        <input v-model="form.company_name" type="text" class="form-input" :class="{ 'border-red-500': form.errors.company_name }" />
                        <p v-if="form.errors.company_name" class="mt-1 text-sm text-red-500">{{ form.errors.company_name }}</p>
                    </div>

                    <div>
                        <label class="form-label">Email</label>
                        <input v-model="form.company_email" type="email" class="form-input" :class="{ 'border-red-500': form.errors.company_email }" />
                        <p v-if="form.errors.company_email" class="mt-1 text-sm text-red-500">{{ form.errors.company_email }}</p>
                    </div>

                    <div>
                        <label class="form-label">Phone</label>
                        <input v-model="form.company_phone" type="text" class="form-input" :class="{ 'border-red-500': form.errors.company_phone }" />
                        <p v-if="form.errors.company_phone" class="mt-1 text-sm text-red-500">{{ form.errors.company_phone }}</p>
                    </div>

                    <div>
                        <label class="form-label">Address</label>
                        <input v-model="form.company_address" type="text" class="form-input" :class="{ 'border-red-500': form.errors.company_address }" />
                        <p v-if="form.errors.company_address" class="mt-1 text-sm text-red-500">{{ form.errors.company_address }}</p>
                    </div>

                    <div>
                        <label class="form-label">City</label>
                        <input v-model="form.company_city" type="text" class="form-input" :class="{ 'border-red-500': form.errors.company_city }" />
                        <p v-if="form.errors.company_city" class="mt-1 text-sm text-red-500">{{ form.errors.company_city }}</p>
                    </div>

                    <div>
                        <label class="form-label">Country</label>
                        <input v-model="form.company_country" type="text" class="form-input" :class="{ 'border-red-500': form.errors.company_country }" />
                        <p v-if="form.errors.company_country" class="mt-1 text-sm text-red-500">{{ form.errors.company_country }}</p>
                    </div>

                    <div>
                        <label class="form-label">Tax Number</label>
                        <input v-model="form.tax_number" type="text" class="form-input" :class="{ 'border-red-500': form.errors.tax_number }" />
                        <p v-if="form.errors.tax_number" class="mt-1 text-sm text-red-500">{{ form.errors.tax_number }}</p>
                    </div>

                    <div>
                        <label class="form-label">Currency</label>
                        <input v-model="form.currency" type="text" class="form-input" :class="{ 'border-red-500': form.errors.currency }" />
                        <p v-if="form.errors.currency" class="mt-1 text-sm text-red-500">{{ form.errors.currency }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Settings' }}
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
    settings: Object,
});

const toast = useToast();

const form = useForm({
    company_name: props.company?.name || props.settings?.company_name || '',
    company_email: props.company?.email || props.settings?.company_email || '',
    company_phone: props.company?.phone || props.settings?.company_phone || '',
    company_address: props.company?.address || props.settings?.company_address || '',
    company_city: props.company?.city || props.settings?.company_city || '',
    company_country: props.company?.country || props.settings?.company_country || '',
    tax_number: props.company?.tax_number || props.settings?.tax_number || '',
    currency: props.settings?.currency || 'NGN',
});

const submit = () => {
    form.put(route('settings.update'), {
        onSuccess: () => {
            toast.success('Settings updated successfully!');
        },
        onError: () => {
            toast.error('Failed to update settings. Please check the errors.');
        },
    });
};
</script>
