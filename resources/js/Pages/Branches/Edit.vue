<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Edit Branch" subtitle="Update branch information and settings">
            <template #actions>
                <Link :href="route('branches.index')" class="btn btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </Link>
            </template>
        </PageHeader>

        <div class="card rounded-2xl p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Name *</label>
                        <input v-model="form.name" type="text" class="form-input" :class="{ 'border-red-500': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="form-label">Code *</label>
                        <input v-model="form.code" type="text" class="form-input" :class="{ 'border-red-500': form.errors.code }" />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-500">{{ form.errors.code }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Address Line 1 *</label>
                        <input v-model="form.address_line_1" type="text" class="form-input" :class="{ 'border-red-500': form.errors.address_line_1 }" />
                        <p v-if="form.errors.address_line_1" class="mt-1 text-sm text-red-500">{{ form.errors.address_line_1 }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Address Line 2</label>
                        <input v-model="form.address_line_2" type="text" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">City *</label>
                        <input v-model="form.city" type="text" class="form-input" :class="{ 'border-red-500': form.errors.city }" />
                        <p v-if="form.errors.city" class="mt-1 text-sm text-red-500">{{ form.errors.city }}</p>
                    </div>

                    <div>
                        <label class="form-label">State/Province</label>
                        <input v-model="form.state" type="text" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Postal Code</label>
                        <input v-model="form.postal_code" type="text" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Country *</label>
                        <input v-model="form.country" type="text" class="form-input" :class="{ 'border-red-500': form.errors.country }" />
                        <p v-if="form.errors.country" class="mt-1 text-sm text-red-500">{{ form.errors.country }}</p>
                    </div>

                    <div>
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" type="text" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Email</label>
                        <input v-model="form.email" type="email" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Manager</label>
                        <select v-model="form.manager_id" class="form-input">
                            <option value="">Select Manager</option>
                            <option v-for="manager in managers" :key="manager.id" :value="manager.id">
                                {{ manager.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select v-model="form.status" class="form-input">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Link :href="route('branches.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Branch' }}
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
    branch: Object,
    managers: Array,
});

const toast = useToast();

const form = useForm({
    name: props.branch.name,
    code: props.branch.code,
    address_line_1: props.branch.address_line_1 || '',
    address_line_2: props.branch.address_line_2 || '',
    city: props.branch.city || '',
    state: props.branch.state || '',
    postal_code: props.branch.postal_code || '',
    country: props.branch.country || '',
    phone: props.branch.phone || '',
    email: props.branch.email || '',
    manager_id: props.branch.manager_id || '',
    status: props.branch.status,
});

const submit = () => {
    form.put(route('branches.update', props.branch.id), {
        onSuccess: () => {
            toast.success('Branch updated successfully.');
        },
    });
};
</script>
