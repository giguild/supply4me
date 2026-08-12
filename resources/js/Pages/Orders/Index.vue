<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Orders">
            <template #actions>
                <Link :href="route('orders.create')" class="btn btn-accent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Order
                </Link>
            </template>
        </PageHeader>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
            <div class="flex-1">
                <SearchInput v-model="search" />
            </div>
            <select v-model="statusFilter" class="form-input w-full sm:w-48" @change="fetchOrders">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <DataTable :columns="columns" :data="orders.data" :meta="meta" @page="goToPage">
            <template #cell-order_number="{ row }">
                <Link :href="route('orders.show', row.id)" class="text-sm font-medium text-accent hover:underline">
                    {{ row.order_number }}
                </Link>
            </template>

            <template #cell-customer="{ row }">
                <span class="text-sm text-gray-900">{{ row.customer?.name }}</span>
            </template>

            <template #cell-order_date="{ row }">
                <span class="text-sm text-gray-500">{{ row.order_date }}</span>
            </template>

            <template #cell-total_amount="{ row }">
                <span class="text-sm font-medium text-gray-900">{{ formatCurrency(row.total_amount) }}</span>
            </template>

            <template #cell-status="{ row }">
                <StatusBadge :value="row.status" />
            </template>

            <template #cell-payment_status="{ row }">
                <StatusBadge :value="row.payment_status" />
            </template>

            <template #cell-fulfillment_status="{ row }">
                <StatusBadge :value="row.fulfillment_status" />
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('orders.show', row.id)" class="text-sm text-accent hover:underline">View</Link>
                    <button
                        v-if="row.status === 'pending'"
                        @click="confirmOrder(row.id)"
                        class="btn btn-sm"
                        style="background: #d4edda; color: #155724;"
                    >Confirm</button>
                    <button
                        v-if="row.status !== 'completed' && row.status !== 'cancelled'"
                        @click="cancelOrder(row.id)"
                        class="btn btn-sm btn-danger"
                    >Cancel</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No orders found" description="Create your first order to get started.">
                    <template #action>
                        <Link :href="route('orders.create')" class="btn btn-accent">New Order</Link>
                    </template>
                </EmptyState>
            </template>
        </DataTable>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import SearchInput from '@/Components/UI/SearchInput.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    orders: Object,
    filters: Object,
});

const toast = useToast();
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const columns = [
    { key: 'order_number', label: 'Order#' },
    { key: 'customer', label: 'Customer' },
    { key: 'order_date', label: 'Date' },
    { key: 'total_amount', label: 'Total' },
    { key: 'status', label: 'Status' },
    { key: 'payment_status', label: 'Payment' },
    { key: 'fulfillment_status', label: 'Fulfillment' },
];

const meta = {
    current_page: props.orders.current_page,
    last_page: props.orders.last_page,
    from: props.orders.from,
    to: props.orders.to,
    total: props.orders.total,
};

let debounceTimer = null;
const debouncedFetch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchOrders, 300);
};

const fetchOrders = () => {
    router.get(route('orders.index'), {
        search: search.value,
        status: statusFilter.value,
    }, { preserveState: true, replace: true });
};

const goToPage = (page) => {
    router.get(route('orders.index'), {
        search: search.value,
        status: statusFilter.value,
        page,
    }, { preserveState: true, replace: true });
};

const confirmOrder = (id) => {
    if (confirm('Are you sure you want to confirm this order?')) {
        router.post(route('orders.confirm', id), {}, {
            onSuccess: () => toast.success('Order confirmed successfully'),
            onError: () => toast.error('Failed to confirm order'),
        });
    }
};

const cancelOrder = (id) => {
    if (confirm('Are you sure you want to cancel this order?')) {
        router.post(route('orders.cancel', id), {}, {
            onSuccess: () => toast.success('Order cancelled successfully'),
            onError: () => toast.error('Failed to cancel order'),
        });
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount || 0);
};
</script>
