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
                                {{ order.order_number }} — {{ order.customer?.name ?? 'No customer' }}
                            </option>
                        </select>
                        <p v-if="form.errors.order_id" class="mt-1 text-sm text-red-500">{{ form.errors.order_id }}</p>
                    </div>

                    <div>
                        <label class="form-label">Warehouse *</label>
                        <select v-model="form.warehouse_id" class="form-input" :class="{ 'border-red-500': form.errors.warehouse_id }">
                            <option value="">Select Warehouse</option>
                            <option v-for="warehouse in warehouses" :key="warehouse.id" :value="warehouse.id">
                                {{ warehouse.name }}
                            </option>
                        </select>
                        <p v-if="form.errors.warehouse_id" class="mt-1 text-sm text-red-500">{{ form.errors.warehouse_id }}</p>
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
                        <p v-if="carriers.length === 0" class="mt-1 text-sm text-amber-600">
                            No carriers yet — add one under Shipping → Carriers first.
                        </p>
                    </div>

                    <div>
                        <label class="form-label">Tracking Number</label>
                        <div class="flex gap-2">
                            <input v-model="form.tracking_number" type="text" class="form-input flex-1" />
                            <button type="button" class="btn btn-outline shrink-0" @click="generateTrackingNumber">Generate</button>
                        </div>
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
                        <label class="form-label">Estimated Delivery</label>
                        <input v-model="form.estimated_delivery_date" type="date" class="form-input" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Notes</label>
                        <textarea v-model="form.notes" rows="3" class="form-input" />
                    </div>
                </div>

                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    Shipment items are generated automatically from the order's line items.
                </p>

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
    warehouses: Array,
    carriers: Array,
});

const toast = useToast();

const form = useForm({
    order_id: '',
    warehouse_id: '',
    carrier_id: '',
    tracking_number: '',
    shipping_method: '',
    estimated_delivery_date: '',
    notes: '',
});

const submit = () => {
    form.post(route('shipments.store'), {
        onSuccess: () => {
            toast.success('Shipment created successfully.');
        },
    });
};

const generateTrackingNumber = () => {
    const carrier = props.carriers.find((c) => c.id === form.carrier_id);
    const prefix = carrier?.code ? carrier.code.replace(/[^A-Za-z0-9]/g, '').toUpperCase() : 'TRK';
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ0123456789';
    let suffix = '';
    for (let i = 0; i < 12; i++) {
        suffix += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    form.tracking_number = `${prefix}${suffix}`;
};
</script>