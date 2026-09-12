<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="New Stock Transfer">
                <template #actions>
                    <Link :href="route('stock.transfers')" class="btn btn-outline btn-sm">Back</Link>
                </template>
            </PageHeader>

            <form @submit.prevent="submit">
                <div class="card p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Transfer Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">From Warehouse *</label>
                            <select v-model="form.from_warehouse_id" class="form-input" required>
                                <option value="">Select Source</option>
                                <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{ wh.name }}</option>
                            </select>
                            <p v-if="form.errors.from_warehouse_id" class="mt-1 text-sm text-red-600">{{ form.errors.from_warehouse_id }}</p>
                        </div>

                        <div>
                            <label class="form-label">To Warehouse *</label>
                            <select v-model="form.to_warehouse_id" class="form-input" required>
                                <option value="">Select Destination</option>
                                <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{ wh.name }}</option>
                            </select>
                            <p v-if="form.errors.to_warehouse_id" class="mt-1 text-sm text-red-600">{{ form.errors.to_warehouse_id }}</p>
                        </div>

                        <div>
                            <label class="form-label">Notes</label>
                            <input v-model="form.notes" type="text" class="form-input" />
                        </div>
                    </div>
                </div>

                <div class="card p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">Items</h3>
                        <button type="button" @click="addItem" class="btn btn-outline btn-sm">+ Add Item</button>
                    </div>

                    <EmptyState
                        v-if="form.items.length === 0"
                        title="No items added yet"
                        description='Click "+ Add Item" to add products to transfer.'
                    />

                    <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 pb-4 border-b last:border-b-0">
                        <div>
                            <label class="form-label">Product *</label>
                            <select v-model="item.product_id" class="form-input" required>
                                <option value="">Select</option>
                                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                            <p v-if="form.errors[`items.${index}.product_id`]" class="mt-1 text-sm text-red-600">{{ form.errors[`items.${index}.product_id`] }}</p>
                        </div>
                        <div>
                            <label class="form-label">Quantity *</label>
                            <input v-model.number="item.quantity" type="number" min="0.01" step="0.01" class="form-input" required />
                            <p v-if="form.errors[`items.${index}.quantity`]" class="mt-1 text-sm text-red-600">{{ form.errors[`items.${index}.quantity`] }}</p>
                        </div>
                        <div class="flex items-end">
                            <button type="button" @click="removeItem(index)" class="btn btn-danger btn-sm">Remove</button>
                        </div>
                    </div>

                    <p v-if="form.errors.items" class="mt-1 text-sm text-red-600">{{ form.errors.items }}</p>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('stock.transfers')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="btn btn-accent">
                        {{ form.processing ? 'Saving...' : 'Create Transfer' }}
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

defineProps({
    warehouses: Array,
    products: Array,
});

const form = useForm({
    from_warehouse_id: '',
    to_warehouse_id: '',
    notes: '',
    items: [],
});

const addItem = () => {
    form.items.push({ product_id: '', quantity: 1 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('stock.transfers.store'));
};
</script>