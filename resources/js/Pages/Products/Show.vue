<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="`Product: ${product.name}`">
            <template #actions>
                <Link :href="route('products.edit', product.id)" class="btn btn-accent">Edit</Link>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatCard label="Cost Price" :value="formatCurrency(product.cost_price)" />
            <StatCard label="Selling Price" :value="formatCurrency(product.selling_price)" />
            <StatCard label="Stock Level" :value="totalStock" />
            <StatCard label="Reorder Level" :value="product.reorder_level ?? '-'" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="card p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Product Information</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">SKU</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.sku || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Barcode</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.barcode || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Category</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.category?.name || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Brand</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.brand?.name || '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Unit</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.unit ? `${product.unit.name} (${product.unit.short_name})` : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Type</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.product_type || 'Standard' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500">Description</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.description || '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Status</h3>
                    <StatusBadge :value="product.status || 'active'" />
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <span :class="product.is_sellable ? 'text-green-600' : 'text-gray-400'">{{ product.is_sellable ? '\u2713' : '\u2717' }} Sellable</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span :class="product.is_purchasable ? 'text-green-600' : 'text-gray-400'">{{ product.is_purchasable ? '\u2713' : '\u2717' }} Purchasable</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span :class="product.is_stockable ? 'text-green-600' : 'text-gray-400'">{{ product.is_stockable ? '\u2713' : '\u2717' }} Stockable</span>
                        </div>
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Pricing</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Cost Price</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(product.cost_price) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Selling Price</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ formatCurrency(product.selling_price) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Minimum Price</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.minimum_price ? formatCurrency(product.minimum_price) : '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Tax Rate</dt>
                            <dd class="font-medium text-gray-900 dark:text-gray-100">{{ product.tax_rate ? product.tax_rate + '%' : '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Stock Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Warehouse</th>
                            <th>Quantity</th>
                            <th>Reserved</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!product.stockItems || product.stockItems.length === 0">
                            <td colspan="4">
                                <div class="text-center py-8">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">No stock records</p>
                                    <p class="text-sm text-gray-500">Stock records will appear here once this product is added to a warehouse.</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="item in product.stockItems" :key="item.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ item.warehouse?.name || '-' }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.reserved_quantity || 0 }}</td>
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ item.quantity - (item.reserved_quantity || 0) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    product: Object,
});

const totalStock = computed(() => {
    if (!props.product.stockItems) return 0;
    return props.product.stockItems.reduce((sum, item) => sum + item.quantity, 0);
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(value || 0);
};
</script>
