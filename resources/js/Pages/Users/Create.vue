<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="New User" subtitle="Add a new user to the system">
            <template #actions>
                <Link :href="route('users.index')" class="btn btn-outline">
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
                        <label class="form-label">Email *</label>
                        <input v-model="form.email" type="email" class="form-input" :class="{ 'border-red-500': form.errors.email }" />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="form-label">Password *</label>
                        <input v-model="form.password" type="password" class="form-input" :class="{ 'border-red-500': form.errors.password }" />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label class="form-label">Confirm Password *</label>
                        <input v-model="form.password_confirmation" type="password" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Phone</label>
                        <input v-model="form.phone" type="text" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Role *</label>
                        <select v-model="form.role" class="form-input" :class="{ 'border-red-500': form.errors.role }">
                            <option value="">Select Role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.role" class="mt-1 text-sm text-red-500">{{ form.errors.role }}</p>
                    </div>

                    <div>
                        <label class="form-label">Company</label>
                        <select v-model="form.company_id" class="form-input">
                            <option value="">Select Company</option>
                            <option v-for="company in companies" :key="company.id" :value="company.id">
                                {{ company.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Branch</label>
                        <select v-model="form.branch_id" class="form-input">
                            <option value="">Select Branch</option>
                            <option v-for="branch in branches" :key="branch.id" :value="branch.id">
                                {{ branch.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select v-model="form.status" class="form-input">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Link :href="route('users.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create User' }}
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
    roles: Array,
    companies: Array,
    branches: Array,
});

const toast = useToast();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    role: '',
    company_id: '',
    branch_id: '',
    status: 'active',
});

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            toast.success('User created successfully.');
        },
    });
};
</script>
