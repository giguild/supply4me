<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Edit Delivery Route" subtitle="Update route information and stops">
            <template #actions>
                <Link :href="route('delivery-routes.index')" class="btn btn-outline">
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
                        <label class="form-label">Route Name *</label>
                        <input v-model="form.name" type="text" class="form-input" :class="{ 'border-red-500': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select v-model="form.status" class="form-input">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Description</label>
                        <textarea v-model="form.description" rows="2" class="form-input" />
                    </div>
                </div>

                <div class="mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Route Stops</h3>
                        <button type="button" class="btn btn-outline btn-sm" @click="addStop">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Stop
                        </button>
                    </div>

                    <div v-for="(stop, index) in form.stops" :key="index" class="flex items-start gap-4 mb-4 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 w-10 h-10 bg-accent text-white rounded-full flex items-center justify-center font-bold text-sm">
                            {{ index + 1 }}
                        </div>
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Address *</label>
                                <input v-model="stop.address" type="text" class="form-input" placeholder="Enter address" />
                            </div>
                            <div>
                                <label class="form-label">Sequence Order</label>
                                <input v-model="stop.sequence_order" type="number" min="1" class="form-input" />
                            </div>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 mt-6" @click="removeStop(index)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <EmptyState
                        v-if="form.stops.length === 0"
                        title="No stops added"
                        description="Click 'Add Stop' to begin building your route."
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Link :href="route('delivery-routes.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Updating...' : 'Update Route' }}
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
import EmptyState from '@/Components/UI/EmptyState.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    deliveryRoute: Object,
});

const toast = useToast();

const form = useForm({
    name: props.deliveryRoute.name,
    description: props.deliveryRoute.description || '',
    status: props.deliveryRoute.status,
    stops: props.deliveryRoute.stops?.map(stop => ({
        id: stop.id,
        address: stop.address,
        sequence_order: stop.sequence_order,
    })) || [],
});

const addStop = () => {
    form.stops.push({
        address: '',
        sequence_order: form.stops.length + 1,
    });
};

const removeStop = (index) => {
    form.stops.splice(index, 1);
    form.stops.forEach((stop, i) => {
        stop.sequence_order = i + 1;
    });
};

const submit = () => {
    form.put(route('delivery-routes.update', props.deliveryRoute.id), {
        onSuccess: () => {
            toast.success('Delivery route updated successfully.');
        },
    });
};
</script>
