<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="New Pick List">
                <template #actions>
                    <Link :href="route('pick-lists.index')" class="btn btn-outline btn-sm">Back</Link>
                </template>
            </PageHeader>

            <form @submit.prevent="submit">
                <div class="card p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Pick List Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Order *</label>
                            <select v-model="form.order_id" class="form-input" required>
                                <option value="">Select Order</option>
                                <option v-for="o in orders" :key="o.id" :value="o.id">{{ o.order_number }}</option>
                            </select>
                            <p v-if="form.errors.order_id" class="mt-1 text-sm text-red-600">{{ form.errors.order_id }}</p>
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
                        description='Click "+ Add Item" to begin adding pick list items.'
                    />

                    <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 pb-4 border-b last:border-b-0">
                        <div>
                            <label class="form-label">Product *</label>
                            <select v-model="item.product_id" class="form-input" required>
                                <option value="">Select</option>
                                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Quantity to Pick *</label>
                            <input v-model.number="item.quantity" type="number" min="1" class="form-input" required />
                        </div>
                        <div class="flex items-end gap-2">
                            <div class="flex-1">
                                <label class="form-label">Bin Location</label>
                                <input v-model="item.bin_location" type="text" class="form-input" />
                            </div>
                            <button type="button" @click="removeItem(index)" class="btn btn-danger btn-sm">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('pick-lists.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="btn btn-accent">
                        {{ form.processing ? 'Saving...' : 'Create Pick List' }}
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
    orders: Array,
    products: Array,
});

const form = useForm({
    order_id: '',
    notes: '',
    items: [],
});

const addItem = () => {
    form.items.push({
        product_id: '',
        quantity: 1,
        bin_location: '',
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('pick-lists.store'));
};
</script>
