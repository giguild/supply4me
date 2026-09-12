<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
            <PageHeader :title="`Pick List ${pickList.pick_list_number}`">
                <template #actions>
                    <Link :href="route('pick-lists.index')" class="btn btn-outline btn-sm">Back</Link>
                    <button
                        v-if="pickList.status === 'pending' || pickList.status === 'draft'"
                        type="button"
                        class="btn btn-accent btn-sm"
                        @click="start"
                    >
                        Start Picking
                    </button>
                    <button
                        v-if="pickList.status === 'in_progress'"
                        type="button"
                        class="btn btn-accent btn-sm"
                        @click="complete"
                    >
                        Complete
                    </button>
                </template>
            </PageHeader>

            <div class="card p-6 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Order</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ pickList.order?.order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</p>
                        <StatusBadge :value="pickList.status" :label="pickList.status" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Assigned To</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ pickList.picker?.name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Warehouse</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ pickList.warehouse?.name ?? '-' }}</p>
                    </div>
                </div>
                <div v-if="pickList.notes" class="mt-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                    <p class="text-sm text-gray-900">{{ pickList.notes }}</p>
                </div>
            </div>

            <div class="card overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold">Items</h3>
                </div>
                <DataTable :columns="itemColumns" :data="pickList.items || []">
                    <template #cell-product="{ row }">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ row.product?.name }}</span>
                    </template>
                    <template #cell-bin="{ row }">
                        <span class="text-gray-500 dark:text-gray-400">{{ row.bin?.code ?? '-' }}</span>
                    </template>
                    <template #cell-status="{ row }">
                        <StatusBadge :value="row.status ?? 'pending'" :label="row.status ?? 'pending'" />
                    </template>
                </DataTable>
            </div>

            <div v-if="pickList.status === 'in_progress'" class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Record Picks</h3>
                <form @submit.prevent="savePicks">
                    <div v-for="(item, index) in pickForm.items" :key="item.id" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 pb-4 border-b last:border-b-0">
                        <div>
                            <label class="form-label">Product</label>
                            <p class="text-sm text-gray-900 dark:text-gray-100 pt-2">{{ item.product_name }}</p>
                        </div>
                        <div>
                            <label class="form-label">To Pick</label>
                            <p class="text-sm text-gray-900 dark:text-gray-100 pt-2">{{ item.quantity_to_pick }}</p>
                        </div>
                        <div>
                            <label class="form-label">Quantity Picked</label>
                            <input v-model.number="item.quantity_picked" type="number" min="0" :max="item.quantity_to_pick" step="any" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Notes</label>
                            <input v-model="item.notes" type="text" class="form-input" />
                        </div>
                    </div>

                    <p v-if="updateForm.errors.items" class="text-sm text-red-600 mb-4">{{ updateForm.errors.items }}</p>

                    <div class="flex items-center justify-end gap-3">
                        <button type="submit" :disabled="updateForm.processing" class="btn btn-accent">
                            {{ updateForm.processing ? 'Saving...' : 'Save Picks' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { router, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { ref } from 'vue';

const props = defineProps({
    pickList: Object,
});

const itemColumns = [
    { key: 'product', label: 'Product' },
    { key: 'quantity_to_pick', label: 'Qty To Pick' },
    { key: 'quantity_picked', label: 'Picked' },
    { key: 'bin', label: 'Bin' },
    { key: 'status', label: 'Status' },
];

const pickForm = ref({
    items: (props.pickList.items || []).map((item) => ({
        id: item.id,
        product_name: item.product?.name ?? '-',
        quantity_to_pick: item.quantity_to_pick,
        quantity_picked: Number(item.quantity_picked ?? 0),
        notes: item.notes ?? '',
    })),
});

const updateForm = useForm({
    items: [],
});

const start = () => {
    if (confirm(`Start pick list ${props.pickList.pick_list_number}?`)) {
        router.post(route('pick-lists.start', props.pickList.id), {}, { preserveScroll: true });
    }
};

const complete = () => {
    if (confirm(`Complete pick list ${props.pickList.pick_list_number}?`)) {
        router.post(route('pick-lists.complete', props.pickList.id), {}, { preserveScroll: true });
    }
};

const savePicks = () => {
    const changed = pickForm.value.items
        .filter((item) => item.quantity_picked !== Number(props.pickList.items?.find((o) => o.id === item.id)?.quantity_picked ?? 0) || item.notes !== '')
        .map((item) => ({
            id: item.id,
            quantity_picked: item.quantity_picked,
            notes: item.notes || null,
        }));

    if (changed.length === 0) {
        return;
    }

    updateForm.transform(() => ({ items: changed }));
    updateForm.put(route('pick-lists.update', props.pickList.id), { preserveScroll: true });
};
</script>