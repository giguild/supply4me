<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <PageHeader title="Edit Warehouse">
                <template #actions>
                    <Link :href="route('warehouses.index')" class="btn btn-outline">Back to List</Link>
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
                            <label class="form-label">Code *</label>
                            <input v-model="form.code" type="text" class="form-input" required />
                            <p v-if="form.errors.code" class="text-red-500 text-xs mt-1">{{ form.errors.code }}</p>
                        </div>
                        <div>
                            <label class="form-label">Branch</label>
                            <select v-model="form.branch_id" class="form-input">
                                <option value="">Select Branch</option>
                                <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Type *</label>
                            <select v-model="form.type" class="form-input" required>
                                <option value="main">Main</option>
                                <option value="regional">Regional</option>
                                <option value="temporary">Temporary</option>
                                <option value="dropship">Dropship</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Location</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="form-label">Address</label>
                            <input v-model="form.address" type="text" class="form-input" />
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
                            <label class="form-label">Country</label>
                            <input v-model="form.country" type="text" class="form-input" />
                        </div>
                    </div>
                </div>

                <!-- Capacity & Status -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Capacity & Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Capacity</label>
                            <input v-model="form.capacity" type="number" min="0" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Status *</label>
                            <select v-model="form.status" class="form-input" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <Link :href="route('warehouses.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-accent" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Update Warehouse' }}
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
    warehouse: Object,
    branches: Array,
});

const toast = useToast();

const form = useForm({
    name: props.warehouse.name,
    code: props.warehouse.code,
    branch_id: props.warehouse.branch_id || '',
    type: props.warehouse.type,
    address: props.warehouse.address || '',
    city: props.warehouse.city || '',
    state: props.warehouse.state || '',
    country: props.warehouse.country || 'Nigeria',
    capacity: props.warehouse.capacity || '',
    status: props.warehouse.status,
});

const submit = () => {
    form.put(route('warehouses.update', props.warehouse.id), {
        onSuccess: () => toast.success('Warehouse updated successfully.'),
    });
};
</script>
