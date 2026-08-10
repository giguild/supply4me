<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="New GRN">
                <template #actions>
                    <Link :href="route('grn.index')" class="btn btn-outline btn-sm">Back</Link>
                </template>
            </PageHeader>

            <form @submit.prevent="submit">
                <div class="card p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">GRN Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Supplier *</label>
                            <select v-model="form.supplier_id" class="form-input" required>
                                <option value="">Select Supplier</option>
                                <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                            </select>
                            <p v-if="form.errors.supplier_id" class="mt-1 text-sm text-red-600">{{ form.errors.supplier_id }}</p>
                        </div>

                        <div>
                            <label class="form-label">Warehouse *</label>
                            <select v-model="form.warehouse_id" class="form-input" required>
                                <option value="">Select Warehouse</option>
                                <option v-for="wh in warehouses" :key="wh.id" :value="wh.id">{{ wh.name }}</option>
                            </select>
                            <p v-if="form.errors.warehouse_id" class="mt-1 text-sm text-red-600">{{ form.errors.warehouse_id }}</p>
                        </div>

                        <div>
                            <label class="form-label">Expected Date *</label>
                            <input v-model="form.expected_date" type="date" class="form-input" required />
                            <p v-if="form.errors.expected_date" class="mt-1 text-sm text-red-600">{{ form.errors.expected_date }}</p>
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
                        description='Click "+ Add Item" to begin adding GRN items.'
                    />

                    <div v-for="(item, index) in form.items" :key="index" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 pb-4 border-b last:border-b-0">
                        <div>
                            <label class="form-label">Product *</label>
                            <select v-model="item.product_id" class="form-input" required>
                                <option value="">Select</option>
                                <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Expected Qty *</label>
                            <input v-model.number="item.expected_qty" type="number" min="1" class="form-input" required />
                        </div>
                        <div>
                            <label class="form-label">Received Qty *</label>
                            <input v-model.number="item.received_qty" type="number" min="0" class="form-input" required />
                        </div>
                        <div>
                            <label class="form-label">Unit Cost *</label>
                            <input v-model.number="item.unit_cost" type="number" step="0.01" min="0" class="form-input" required />
                        </div>
                        <div class="flex items-end">
                            <button type="button" @click="removeItem(index)" class="btn btn-danger btn-sm">Remove</button>
                        </div>
                    </div>

                    <p v-if="form.errors.items" class="mt-1 text-sm text-red-600">{{ form.errors.items }}</p>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('grn.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="btn btn-accent">
                        {{ form.processing ? 'Saving...' : 'Create GRN' }}
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
    suppliers: Array,
    products: Array,
    warehouses: Array,
});

const form = useForm({
    supplier_id: '',
    warehouse_id: '',
    expected_date: '',
    notes: '',
    items: [],
});

const addItem = () => {
    form.items.push({
        product_id: '',
        expected_qty: 1,
        received_qty: 0,
        unit_cost: 0,
    });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('grn.store'));
};
</script>
