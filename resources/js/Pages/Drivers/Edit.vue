<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Edit Driver" subtitle="Update driver information">
            <template #actions>
                <Link :href="route('drivers.index')" class="btn btn-outline">
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
                        <label class="form-label">Phone *</label>
                        <input v-model="form.phone" type="text" class="form-input" :class="{ 'border-red-500': form.errors.phone }" />
                        <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label class="form-label">Email</label>
                        <input v-model="form.email" type="email" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Vehicle Type *</label>
                        <select v-model="form.vehicle_type" class="form-input" :class="{ 'border-red-500': form.errors.vehicle_type }">
                            <option value="">Select Vehicle Type</option>
                            <option value="motorcycle">Motorcycle</option>
                            <option value="van">Van</option>
                            <option value="truck">Truck</option>
                            <option value="pickup">Pickup</option>
                        </select>
                        <p v-if="form.errors.vehicle_type" class="mt-1 text-sm text-red-500">{{ form.errors.vehicle_type }}</p>
                    </div>

                    <div>
                        <label class="form-label">Vehicle Registration *</label>
                        <input v-model="form.vehicle_registration" type="text" class="form-input" :class="{ 'border-red-500': form.errors.vehicle_registration }" />
                        <p v-if="form.errors.vehicle_registration" class="mt-1 text-sm text-red-500">{{ form.errors.vehicle_registration }}</p>
                    </div>

                    <div>
                        <label class="form-label">License Number *</label>
                        <input v-model="form.license_number" type="text" class="form-input" :class="{ 'border-red-500': form.errors.license_number }" />
                        <p v-if="form.errors.license_number" class="mt-1 text-sm text-red-500">{{ form.errors.license_number }}</p>
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select v-model="form.status" class="form-input">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="on_leave">On Leave</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Link :href="route('drivers.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Driver' }}
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
    driver: Object,
});

const toast = useToast();

const form = useForm({
    name: props.driver.name,
    phone: props.driver.phone,
    email: props.driver.email || '',
    vehicle_type: props.driver.vehicle_type,
    vehicle_registration: props.driver.vehicle_registration,
    license_number: props.driver.license_number,
    status: props.driver.status,
});

const submit = () => {
    form.put(route('drivers.update', props.driver.id), {
        onSuccess: () => {
            toast.success('Driver updated successfully.');
        },
    });
};
</script>
