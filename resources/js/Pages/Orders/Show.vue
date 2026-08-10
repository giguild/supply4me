<template>
    <AppLayout :user="$page.props.auth.user">
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('orders.index')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
            <PageHeader :title="`Order ${order.order_number}`" />
            <StatusBadge :value="order.status" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Customer</p>
                <p class="text-sm font-semibold text-gray-900">{{ order.customer?.name }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Date</p>
                <p class="text-sm font-semibold text-gray-900">{{ order.order_date }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total</p>
                <p class="text-sm font-bold text-accent">{{ formatCurrency(order.total_amount) }}</p>
            </div>
            <div class="card p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Payment Status</p>
                <StatusBadge :value="order.payment_status" />
            </div>
        </div>

        <div class="card overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-semibold text-gray-900">Order Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Tax</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="!order.items || order.items.length === 0">
                            <td colspan="7">
                                <div class="text-center py-8">
                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    <p class="text-sm font-medium text-gray-900 mb-1">No items</p>
                                    <p class="text-sm text-gray-500">This order has no items yet.</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="item in order.items" :key="item.id">
                            <td class="font-medium text-gray-900">{{ item.name }}</td>
                            <td class="text-gray-500">{{ item.sku }}</td>
                            <td class="text-gray-500">{{ item.quantity }}</td>
                            <td class="text-gray-500">{{ formatCurrency(item.unit_price) }}</td>
                            <td class="text-gray-500">{{ formatCurrency(item.tax_amount) }}</td>
                            <td class="text-gray-500">{{ formatCurrency(item.discount_amount) }}</td>
                            <td class="font-medium text-gray-900">{{ formatCurrency(item.line_total) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="order.statusHistory?.length" class="card p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-5">Status History</h3>
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                <div v-for="(history, index) in order.statusHistory" :key="history.id" class="relative pl-10 pb-6 last:pb-0">
                    <div class="absolute left-2.5 top-1 w-3 h-3 rounded-full border-2 border-white"
                        :class="index === 0 ? 'bg-accent' : 'bg-gray-300'"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ history.status }}
                            <span v-if="history.previous_status" class="text-gray-500">from {{ history.previous_status }}</span>
                        </p>
                        <p v-if="history.notes" class="text-sm text-gray-500 mt-0.5">{{ history.notes }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ history.created_at }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button v-if="order.status === 'pending'" @click="confirmOrder" class="btn" style="background: #d4edda; color: #155724;">
                Confirm Order
            </button>
            <Link v-if="order.status !== 'completed' && order.status !== 'cancelled'" :href="route('orders.edit', order.id)" class="btn btn-outline">
                Edit
            </Link>
            <button v-if="order.status !== 'completed' && order.status !== 'cancelled'" @click="cancelOrder" class="btn btn-danger">
                Cancel Order
            </button>
        </div>
    </AppLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({ order: Object });
const toast = useToast();

const confirmOrder = () => {
    if (confirm('Are you sure you want to confirm this order?')) {
        router.post(route('orders.confirm', props.order.id), {}, {
            onSuccess: () => toast.success('Order confirmed successfully'),
            onError: () => toast.error('Failed to confirm order'),
        });
    }
};

const cancelOrder = () => {
    if (confirm('Are you sure you want to cancel this order?')) {
        router.post(route('orders.cancel', props.order.id), {}, {
            onSuccess: () => toast.success('Order cancelled successfully'),
            onError: () => toast.error('Failed to cancel order'),
        });
    }
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount || 0);
};
</script>
