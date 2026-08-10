<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="New Shipment" subtitle="Create a new shipment record">
            <template #actions>
                <Link :href="route('shipments.index')" class="btn btn-outline">
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
                        <label class="form-label">Order *</label>
                        <select v-model="form.order_id" class="form-input" :class="{ 'border-red-500': form.errors.order_id }">
                            <option value="">Select Order</option>
                            <option v-for="order in orders" :key="order.id" :value="order.id">
                                {{ order.order_number }}
                            </option>
                        </select>
                        <p v-if="form.errors.order_id" class="mt-1 text-sm text-red-500">{{ form.errors.order_id }}</p>
                    </div>

                    <div>
                        <label class="form-label">Carrier *</label>
                        <select v-model="form.carrier_id" class="form-input" :class="{ 'border-red-500': form.errors.carrier_id }">
                            <option value="">Select Carrier</option>
                            <option v-for="carrier in carriers" :key="carrier.id" :value="carrier.id">
                                {{ carrier.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.carrier_id" class="mt-1 text-sm text-red-500">{{ form.errors.carrier_id }}</p>
                    </div>

                    <div>
                        <label class="form-label">Tracking Number</label>
                        <input v-model="form.tracking_number" type="text" class="form-input" :class="{ 'border-red-500': form.errors.tracking_number }" />
                        <p v-if="form.errors.tracking_number" class="mt-1 text-sm text-red-500">{{ form.errors.tracking_number }}</p>
                    </div>

                    <div>
                        <label class="form-label">Shipping Method</label>
                        <select v-model="form.shipping_method" class="form-input">
                            <option value="">Select Method</option>
                            <option value="standard">Standard</option>
                            <option value="express">Express</option>
                            <option value="overnight">Overnight</option>
                            <option value="freight">Freight</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Ship Date</label>
                        <input v-model="form.ship_date" type="date" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Estimated Delivery</label>
                        <input v-model="form.estimated_delivery" type="date" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select v-model="form.status" class="form-input">
                            <option value="pending">Pending</option>
                            <option value="in_transit">In Transit</option>
                            <option value="out_for_delivery">Out for Delivery</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Notes</label>
                        <textarea v-model="form.notes" rows="3" class="form-input" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Link :href="route('shipments.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Creating...' : 'Create Shipment' }}
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
    orders: Array,
    carriers: Array,
});

const toast = useToast();

const form = useForm({
    order_id: '',
    carrier_id: '',
    tracking_number: '',
    shipping_method: '',
    ship_date: '',
    estimated_delivery: '',
    status: 'pending',
    notes: '',
});

const submit = () => {
    form.post(route('shipments.store'), {
        onSuccess: () => {
            toast.success('Shipment created successfully.');
        },
    });
};
</script>
