<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Invoices">
            <template #actions>
                <Link :href="route('invoices.create')" class="btn btn-accent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Invoice
                </Link>
            </template>
        </PageHeader>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
            <div class="flex-1">
                <SearchInput v-model="search" />
            </div>
            <select v-model="statusFilter" class="form-input w-full sm:w-48" @change="fetchInvoices">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="sent">Sent</option>
                <option value="viewed">Viewed</option>
                <option value="paid">Paid</option>
                <option value="partial">Partial</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
                <option value="void">Void</option>
            </select>
        </div>

        <DataTable :columns="columns" :data="invoices.data" :meta="meta" @page="goToPage">
            <template #cell-invoice_number="{ row }">
                <Link :href="route('invoices.show', row.id)" class="text-sm font-medium text-accent hover:underline">
                    {{ row.invoice_number }}
                </Link>
            </template>

            <template #cell-customer="{ row }">
                <span class="text-sm text-gray-900">{{ row.customer?.name }}</span>
            </template>

            <template #cell-invoice_date="{ row }">
                <span class="text-sm text-gray-500">{{ row.invoice_date }}</span>
            </template>

            <template #cell-total_amount="{ row }">
                <span class="text-sm font-medium text-gray-900">{{ formatCurrency(row.total_amount) }}</span>
            </template>

            <template #cell-paid_amount="{ row }">
                <span class="text-sm text-green-600">{{ formatCurrency(row.paid_amount) }}</span>
            </template>

            <template #cell-due_amount="{ row }">
                <span class="text-sm text-red-600">{{ formatCurrency(row.due_amount) }}</span>
            </template>

            <template #cell-status="{ row }">
                <StatusBadge :value="row.status" />
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('invoices.show', row.id)" class="text-sm text-accent hover:underline">View</Link>
                    <button v-if="row.status === 'draft'" @click="sendInvoice(row.id)" class="btn btn-sm" style="background: #d4edda; color: #155724;">Send</button>
                    <button v-if="row.status !== 'void' && row.status !== 'paid'" @click="voidInvoice(row.id)" class="btn btn-sm btn-danger">Void</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No invoices found" description="Create your first invoice to get started.">
                    <template #action>
                        <Link :href="route('invoices.create')" class="btn btn-accent">New Invoice</Link>
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
    invoices: Object,
    filters: Object,
});

const toast = useToast();
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const columns = [
    { key: 'invoice_number', label: 'Invoice#' },
    { key: 'customer', label: 'Customer' },
    { key: 'invoice_date', label: 'Date' },
    { key: 'total_amount', label: 'Total' },
    { key: 'paid_amount', label: 'Paid' },
    { key: 'due_amount', label: 'Balance' },
    { key: 'status', label: 'Status' },
];

const meta = {
    current_page: props.invoices.current_page,
    last_page: props.invoices.last_page,
    from: props.invoices.from,
    to: props.invoices.to,
    total: props.invoices.total,
};

let debounceTimer = null;
const debouncedFetch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchInvoices, 300);
};

const fetchInvoices = () => {
    router.get(route('invoices.index'), {
        search: search.value,
        status: statusFilter.value,
    }, { preserveState: true, replace: true });
};

const goToPage = (page) => {
    router.get(route('invoices.index'), {
        search: search.value,
        status: statusFilter.value,
        page,
    }, { preserveState: true, replace: true });
};

const sendInvoice = (id) => {
    if (confirm('Are you sure you want to send this invoice?')) {
        router.post(route('invoices.send', id), {}, {
            onSuccess: () => toast.success('Invoice sent successfully'),
            onError: () => toast.error('Failed to send invoice'),
        });
    }
};

const voidInvoice = (id) => {
    if (confirm('Are you sure you want to void this invoice? This action cannot be undone.')) {
        router.post(route('invoices.void', id), {}, {
            onSuccess: () => toast.success('Invoice voided successfully'),
            onError: () => toast.error('Failed to void invoice'),
        });
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount || 0);
};
</script>
