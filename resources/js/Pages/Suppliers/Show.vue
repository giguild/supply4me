<template>
    <AppLayout :user="$page.props.auth.user">
            <PageHeader :title="supplier.name" :subtitle="supplier.trade_name || 'Supplier details'">
                <template #actions>
                    <Link :href="route('suppliers.edit', supplier.id)" class="btn btn-accent">Edit</Link>
                </template>
            </PageHeader>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Contact Information -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Contact Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="text-sm font-medium">{{ supplier.email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Phone</dt>
                            <dd class="text-sm font-medium">{{ supplier.phone || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Mobile</dt>
                            <dd class="text-sm font-medium">{{ supplier.mobile || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Tax Number</dt>
                            <dd class="text-sm font-medium">{{ supplier.tax_number || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Status</dt>
                            <dd><StatusBadge :value="supplier.status" /></dd>
                        </div>
                    </dl>
                </div>

                <!-- Address -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Address</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Street</dt>
                            <dd class="text-sm font-medium">{{ supplier.address_line_1 || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Line 2</dt>
                            <dd class="text-sm font-medium">{{ supplier.address_line_2 || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">City</dt>
                            <dd class="text-sm font-medium">{{ supplier.city || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">State</dt>
                            <dd class="text-sm font-medium">{{ supplier.state || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Postal Code</dt>
                            <dd class="text-sm font-medium">{{ supplier.postal_code || '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Country</dt>
                            <dd class="text-sm font-medium">{{ supplier.country || '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Business Details -->
                <div class="card p-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider mb-4">Business Details</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Payment Terms</dt>
                            <dd class="text-sm font-medium">{{ supplier.payment_terms_days ? supplier.payment_terms_days + ' days' : '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="text-sm font-medium">{{ supplier.created_at ? new Date(supplier.created_at).toLocaleDateString() : '-' }}</dd>
                        </div>
                        <div v-if="supplier.notes">
                            <dt class="text-sm text-gray-500 dark:text-gray-400">Notes</dt>
                            <dd class="text-sm font-medium">{{ supplier.notes }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

defineProps({
    supplier: Object,
});
</script>
