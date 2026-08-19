<template>
    <AppLayout :user="$page.props.auth.user">
            <PageHeader :title="customer.name" :subtitle="customer.trade_name || 'Customer details'">
                <template #actions>
                    <Link :href="route('customers.edit', customer.id)" class="btn btn-accent">Edit</Link>
                </template>
            </PageHeader>

            <!-- Stats Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <StatCard label="Total Orders" :value="stats.total_orders ?? 0" subtitle="all time" />
                <StatCard label="Total Invoices" :value="stats.total_invoices ?? 0" subtitle="all time" />
                <StatCard label="Outstanding Balance" :value="formatCurrency(stats.outstanding_balance ?? 0)" prefix="₦ " :format="false" subtitle="amount due" />
            </div>

            <!-- Tabs -->
            <div class="tab-nav">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    class="tab-item"
                    :class="{ active: activeTab === tab.key }"
                    @click="activeTab = tab.key"
                >
                    {{ tab.label }}
                </button>
            </div>

            <!-- Overview Tab -->
            <div v-if="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Customer Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Type</dt>
                            <dd class="font-medium text-sm capitalize">{{ customer.customer_type }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Status</dt>
                            <dd><StatusBadge :value="customer.status" /></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Email</dt>
                            <dd class="font-medium text-sm">{{ customer.email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Phone</dt>
                            <dd class="font-medium text-sm">{{ customer.phone || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Mobile</dt>
                            <dd class="font-medium text-sm">{{ customer.mobile || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Tax Number</dt>
                            <dd class="font-medium text-sm">{{ customer.tax_number || '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Address</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Street</dt>
                            <dd class="font-medium text-sm">{{ customer.address_line_1 || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Line 2</dt>
                            <dd class="font-medium text-sm">{{ customer.address_line_2 || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">City</dt>
                            <dd class="font-medium text-sm">{{ customer.city || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">State</dt>
                            <dd class="font-medium text-sm">{{ customer.state || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Postal Code</dt>
                            <dd class="font-medium text-sm">{{ customer.postal_code || '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Country</dt>
                            <dd class="font-medium text-sm">{{ customer.country || '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Business Details</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Credit Limit</dt>
                            <dd class="font-medium text-sm">{{ customer.credit_limit ? '₦ ' + Number(customer.credit_limit).toLocaleString() : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Payment Terms</dt>
                            <dd class="font-medium text-sm">{{ customer.payment_terms_days ? customer.payment_terms_days + ' days' : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Discount</dt>
                            <dd class="font-medium text-sm">{{ customer.discount_percentage ? customer.discount_percentage + '%' : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400 text-sm">Assigned To</dt>
                            <dd class="font-medium text-sm">{{ customer.assigned_to?.name || '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Contacts Tab -->
            <div v-if="activeTab === 'contacts'">
                <DataTable :columns="contactColumns" :data="customer.contacts || []">
                    <template #cell-is_primary="{ row }">
                        <span v-if="row.is_primary" class="text-green-600 font-semibold text-sm">Yes</span>
                        <span v-else class="text-gray-400 dark:text-gray-500 text-sm">No</span>
                    </template>

                    <template #empty>
                        <EmptyState title="No contacts" description="No contacts have been added yet." />
                    </template>
                </DataTable>
            </div>

            <!-- Addresses Tab -->
            <div v-if="activeTab === 'addresses'">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="address in customer.shipping_addresses" :key="address.id" class="card p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-sm">{{ address.label || 'Shipping Address' }}</h4>
                            <StatusBadge v-if="address.is_default" value="default" />
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ address.street }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ address.city }}, {{ address.state }} {{ address.postal_code }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ address.country }}</p>
                    </div>
                </div>
                <EmptyState v-if="!customer.shipping_addresses?.length" title="No addresses" description="No shipping addresses found." />
            </div>

            <!-- Orders Tab -->
            <div v-if="activeTab === 'orders'">
                <DataTable :columns="orderColumns" :data="customer.orders || []">
                    <template #cell-order_number="{ row }">
                        <Link :href="route('orders.show', row.id)" class="text-blue-600 font-medium text-sm">{{ row.order_number }}</Link>
                    </template>
                    <template #cell-order_date="{ row }">
                        <span class="text-sm">{{ formatDate(row.order_date) }}</span>
                    </template>
                    <template #cell-status="{ row }">
                        <StatusBadge :value="row.status" />
                    </template>
                    <template #cell-total_amount="{ row }">
                        <span class="font-medium text-sm">₦ {{ formatCurrency(row.total_amount) }}</span>
                    </template>

                    <template #empty>
                        <EmptyState title="No orders" description="No orders found for this customer." />
                    </template>
                </DataTable>
            </div>

            <!-- Invoices Tab -->
            <div v-if="activeTab === 'invoices'">
                <DataTable :columns="invoiceColumns" :data="customer.invoices || []">
                    <template #cell-invoice_number="{ row }">
                        <Link :href="route('invoices.show', row.id)" class="text-blue-600 font-medium text-sm">{{ row.invoice_number }}</Link>
                    </template>
                    <template #cell-invoice_date="{ row }">
                        <span class="text-sm">{{ formatDate(row.invoice_date) }}</span>
                    </template>
                    <template #cell-due_date="{ row }">
                        <span class="text-sm">{{ formatDate(row.due_date) }}</span>
                    </template>
                    <template #cell-status="{ row }">
                        <StatusBadge :value="row.status" />
                    </template>
                    <template #cell-total_amount="{ row }">
                        <span class="font-medium text-sm">₦ {{ formatCurrency(row.total_amount) }}</span>
                    </template>

                    <template #empty>
                        <EmptyState title="No invoices" description="No invoices found for this customer." />
                    </template>
                </DataTable>
            </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';

defineProps({
    customer: Object,
    stats: { type: Object, default: () => ({}) },
});

const activeTab = ref('overview');

const tabs = [
    { key: 'overview', label: 'Overview' },
    { key: 'contacts', label: 'Contacts' },
    { key: 'addresses', label: 'Addresses' },
    { key: 'orders', label: 'Orders' },
    { key: 'invoices', label: 'Invoices' },
];

const contactColumns = [
    { key: 'name', label: 'Name' },
    { key: 'position', label: 'Position' },
    { key: 'email', label: 'Email' },
    { key: 'phone', label: 'Phone' },
    { key: 'is_primary', label: 'Primary' },
];

const orderColumns = [
    { key: 'order_number', label: 'Order #' },
    { key: 'order_date', label: 'Date' },
    { key: 'status', label: 'Status' },
    { key: 'total_amount', label: 'Total' },
];

const invoiceColumns = [
    { key: 'invoice_number', label: 'Invoice #' },
    { key: 'invoice_date', label: 'Date' },
    { key: 'due_date', label: 'Due Date' },
    { key: 'status', label: 'Status' },
    { key: 'total_amount', label: 'Total' },
];

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value || 0);
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>
