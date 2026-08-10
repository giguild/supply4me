<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="New Stock Transfer">
                <template #actions>
                    <Link :href="route('stock.transfers.index')" class="btn btn-outline btn-sm">Back</Link>
                </template>
            </PageHeader>

            <div class="card p-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Product *</label>
                            <select v-model="form.product_id" class="form-input" required>
                                <option value="">Select Product</option>
                                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                            <p v-if="form.errors.product_id" class="mt-1 text-sm text-red-600">{{ form.errors.product_id }}</p>
                        </div>

                        <div>
                            <label class="form-label">Quantity *</label>
                            <input v-model="form.quantity" type="number" min="1" class="form-input" required />
                            <p v-if="form.errors.quantity" class="mt-1 text-sm text-red-600">{{ form.errors.quantity }}</p>
                        </div>

                        <div>
                            <label class="form-label">From Warehouse *</label>
                            <select v-model="form.from_warehouse_id" class="form-input" required>
                                <option value="">Select Warehouse</option>
                                <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{ wh.name }}</option>
                            </select>
                            <p v-if="form.errors.from_warehouse_id" class="mt-1 text-sm text-red-600">{{ form.errors.from_warehouse_id }}</p>
                        </div>

                        <div>
                            <label class="form-label">To Warehouse *</label>
                            <select v-model="form.to_warehouse_id" class="form-input" required>
                                <option value="">Select Warehouse</option>
                                <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{ wh.name }}</option>
                            </select>
                            <p v-if="form.errors.to_warehouse_id" class="mt-1 text-sm text-red-600">{{ form.errors.to_warehouse_id }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Notes</label>
                            <textarea v-model="form.notes" rows="3" class="form-input" />
                            <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <Link :href="route('stock.transfers.index')" class="btn btn-outline">Cancel</Link>
                        <button type="submit" :disabled="form.processing" class="btn btn-accent">
                            {{ form.processing ? 'Saving...' : 'Create Transfer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';

defineProps({
    products: Array,
    warehouses: Array,
});

const form = useForm({
    product_id: '',
    from_warehouse_id: '',
    to_warehouse_id: '',
    quantity: 1,
    notes: '',
});

const submit = () => {
    form.post(route('stock.transfers.store'));
};
</script>
