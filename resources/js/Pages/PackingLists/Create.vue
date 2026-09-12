<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader title="New Packing List">
                <template #actions>
                    <Link :href="route('packing-lists.index')" class="btn btn-outline btn-sm">Back</Link>
                </template>
            </PageHeader>

            <form @submit.prevent="submit">
                <div class="card p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Packing List Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Order *</label>
                            <select v-model="form.order_id" class="form-input" required>
                                <option value="">Select Order</option>
                                <option v-for="o in orders" :key="o.id" :value="o.id">
                                    {{ o.order_number }} — {{ o.customer?.name ?? 'No customer' }} ({{ o.status }})
                                </option>
                            </select>
                            <p v-if="form.errors.order_id" class="mt-1 text-sm text-red-600">{{ form.errors.order_id }}</p>
                        </div>

                        <div>
                            <label class="form-label">Notes</label>
                            <input v-model="form.notes" type="text" class="form-input" />
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Packing list items are generated automatically from the order's line items.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <Link :href="route('packing-lists.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" :disabled="form.processing" class="btn btn-accent">
                        {{ form.processing ? 'Creating...' : 'Create Packing List' }}
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

defineProps({
    orders: Array,
});

const form = useForm({
    order_id: '',
    notes: '',
});

const submit = () => {
    form.post(route('packing-lists.store'));
};
</script>