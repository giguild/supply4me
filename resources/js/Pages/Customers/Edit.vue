<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <PageHeader title="Edit Customer">
                <template #actions>
                    <Link :href="route('customers.index')" class="btn btn-outline">Back to List</Link>
                </template>
            </PageHeader>

            <form @submit.prevent="submit" class="card p-6 space-y-6">
                <div v-if="form.errors.message" class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    {{ form.errors.message }}
                </div>

                <!-- Basic Information -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Name *</label>
                            <input v-model="form.name" type="text" class="form-input" required />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="form-label">Trade Name</label>
                            <input v-model="form.trade_name" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Customer Type *</label>
                            <select v-model="form.customer_type" class="form-input" required>
                                <option value="">Select Type</option>
                                <option value="individual">Individual</option>
                                <option value="company">Company</option>
                                <option value="government">Government</option>
                            </select>
                            <p v-if="form.errors.customer_type" class="text-red-500 text-xs mt-1">{{ form.errors.customer_type }}</p>
                        </div>
                        <div>
                            <label class="form-label">Status *</label>
                            <select v-model="form.status" class="form-input" required>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Email *</label>
                            <input v-model="form.email" type="email" class="form-input" required />
                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="form-label">Phone</label>
                            <input v-model="form.phone" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Mobile</label>
                            <input v-model="form.mobile" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Tax Number</label>
                            <input v-model="form.tax_number" type="text" class="form-input" />
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="form-label">Address Line 1</label>
                            <input v-model="form.address_line_1" type="text" class="form-input" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label">Address Line 2</label>
                            <input v-model="form.address_line_2" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">City</label>
                            <input v-model="form.city" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">State</label>
                            <input v-model="form.state" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Postal Code</label>
                            <input v-model="form.postal_code" type="text" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Country</label>
                            <input v-model="form.country" type="text" class="form-input" />
                        </div>
                    </div>
                </div>

                <!-- Business Details -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Business Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Credit Limit</label>
                            <input v-model="form.credit_limit" type="number" step="0.01" min="0" class="form-input" />
                            <p v-if="form.errors.credit_limit" class="text-red-500 text-xs mt-1">{{ form.errors.credit_limit }}</p>
                        </div>
                        <div>
                            <label class="form-label">Payment Terms (Days)</label>
                            <input v-model="form.payment_terms_days" type="number" min="0" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Discount Percentage</label>
                            <input v-model="form.discount_percentage" type="number" step="0.01" min="0" max="100" class="form-input" />
                            <p v-if="form.errors.discount_percentage" class="text-red-500 text-xs mt-1">{{ form.errors.discount_percentage }}</p>
                        </div>
                        <div>
                            <label class="form-label">Assigned To</label>
                            <select v-model="form.assigned_to" class="form-input">
                                <option value="">None</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <Link :href="route('customers.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-accent" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Update Customer' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    customer: Object,
    users: Array,
});

const toast = useToast();

const form = useForm({
    name: props.customer.name,
    trade_name: props.customer.trade_name || '',
    customer_type: props.customer.customer_type,
    email: props.customer.email,
    phone: props.customer.phone || '',
    mobile: props.customer.mobile || '',
    tax_number: props.customer.tax_number || '',
    address_line_1: props.customer.address_line_1 || '',
    address_line_2: props.customer.address_line_2 || '',
    city: props.customer.city || '',
    state: props.customer.state || '',
    postal_code: props.customer.postal_code || '',
    country: props.customer.country || '',
    credit_limit: props.customer.credit_limit || '',
    payment_terms_days: props.customer.payment_terms_days || '',
    discount_percentage: props.customer.discount_percentage || '',
    assigned_to: props.customer.assigned_to || '',
    status: props.customer.status,
});

const submit = () => {
    form.put(route('customers.update', props.customer.id), {
        onSuccess: () => toast.success('Customer updated successfully.'),
    });
};
</script>
