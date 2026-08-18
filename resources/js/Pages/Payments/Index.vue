<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Payments">
            <template #actions>
                <Link :href="route('payments.create')" class="btn btn-accent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Payment
                </Link>
            </template>
        </PageHeader>

        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
            <div class="flex-1">
                <SearchInput v-model="search" />
            </div>
            <select v-model="statusFilter" class="form-input w-full sm:w-48" @change="fetchPayments">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <DataTable :columns="columns" :data="payments.data" :meta="meta" @page="goToPage">
            <template #cell-payment_number="{ row }">
                <Link :href="route('payments.show', row.id)" class="text-sm font-medium text-accent hover:underline">
                    {{ row.payment_number }}
                </Link>
            </template>

            <template #cell-party="{ row }">
                <span class="text-sm text-gray-900 dark:text-gray-100">{{ row.customer?.name || row.supplier?.name }}</span>
            </template>

            <template #cell-amount="{ row }">
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(row.amount) }}</span>
            </template>

            <template #cell-payment_method="{ row }">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatMethod(row.payment_method) }}</span>
            </template>

            <template #cell-payment_date="{ row }">
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ row.payment_date }}</span>
            </template>

            <template #cell-status="{ row }">
                <StatusBadge :value="row.status" />
            </template>

            <template #actions="{ row }">
                <div class="flex items-center justify-end gap-2">
                    <Link :href="route('payments.show', row.id)" class="text-sm text-accent hover:underline">View</Link>
                    <button v-if="row.status === 'pending' && canApprovePayments" @click="approvePayment(row.id)" class="btn btn-sm" style="background: #d4edda; color: #155724;">Approve</button>
                    <button v-if="row.status === 'pending' && canApprovePayments" @click="rejectPayment(row.id)" class="btn btn-sm btn-danger">Reject</button>
                </div>
            </template>

            <template #empty>
                <EmptyState title="No payments found" description="Record your first payment to get started.">
                    <template #action>
                        <Link :href="route('payments.create')" class="btn btn-accent">New Payment</Link>
                    </template>
                </EmptyState>
            </template>
        </DataTable>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import SearchInput from '@/Components/UI/SearchInput.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    payments: Object,
    filters: Object,
});

const toast = useToast();
const page = usePage();
const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');

const canApprovePayments = computed(() =>
    (page.props.auth.user?.permissions || []).includes('payment.approve')
);

const columns = [
    { key: 'payment_number', label: 'Payment#' },
    { key: 'party', label: 'Customer/Supplier' },
    { key: 'amount', label: 'Amount' },
    { key: 'payment_method', label: 'Method' },
    { key: 'payment_date', label: 'Date' },
    { key: 'status', label: 'Status' },
];

const meta = {
    current_page: props.payments.current_page,
    last_page: props.payments.last_page,
    from: props.payments.from,
    to: props.payments.to,
    total: props.payments.total,
};

let debounceTimer = null;
const debouncedFetch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchPayments, 300);
};

const fetchPayments = () => {
    router.get(route('payments.index'), {
        search: search.value,
        status: statusFilter.value,
    }, { preserveState: true, replace: true });
};

const goToPage = (page) => {
    router.get(route('payments.index'), {
        search: search.value,
        status: statusFilter.value,
        page,
    }, { preserveState: true, replace: true });
};

const approvePayment = (id) => {
    if (confirm('Are you sure you want to approve this payment?')) {
        router.post(route('payments.approve', id), {}, {
            onSuccess: () => toast.success('Payment approved successfully'),
            onError: () => toast.error('Failed to approve payment'),
        });
    }
};

const rejectPayment = (id) => {
    const reason = prompt('Please enter a reason for rejection:');
    if (reason !== null) {
        router.post(route('payments.reject', id), { rejection_reason: reason }, {
            onSuccess: () => toast.success('Payment rejected successfully'),
            onError: () => toast.error('Failed to reject payment'),
        });
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount || 0);
};

const formatMethod = (method) => {
    const methods = {
        cash: 'Cash',
        bank_transfer: 'Bank Transfer',
        credit_card: 'Credit Card',
        check: 'Cheque',
        mobile_money: 'Mobile Money',
        other: 'Other',
    };
    return methods[method] || method;
};
</script>
