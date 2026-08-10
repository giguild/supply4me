<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="`Shipment ${shipment.shipment_number}`" subtitle="Shipment details and tracking information">
            <template #actions>
                <Link :href="route('shipments.index')" class="btn btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </Link>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4">Shipment Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Shipment Number</p>
                        <p class="font-medium">{{ shipment.shipment_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <StatusBadge :value="shipment.status" :label="shipment.status?.replace('_', ' ')" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Order</p>
                        <p class="font-medium">{{ shipment.order?.order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Carrier</p>
                        <p class="font-medium">{{ shipment.carrier?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Shipping Method</p>
                        <p class="font-medium">{{ shipment.shipping_method || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ship Date</p>
                        <p class="font-medium">{{ shipment.ship_date || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Estimated Delivery</p>
                        <p class="font-medium">{{ shipment.estimated_delivery || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Actual Delivery</p>
                        <p class="font-medium">{{ shipment.actual_delivery || 'N/A' }}</p>
                    </div>
                </div>
                <div v-if="shipment.notes" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                    <p class="font-medium">{{ shipment.notes }}</p>
                </div>
            </div>

            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4">Tracking Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tracking Number</p>
                        <p class="font-medium font-mono text-lg">{{ shipment.tracking_number || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Carrier</p>
                        <p class="font-medium">{{ shipment.carrier?.name }}</p>
                    </div>
                    <div v-if="shipment.tracking_url">
                        <a :href="shipment.tracking_url" target="_blank" class="btn btn-accent w-full">
                            Track Shipment
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

const props = defineProps({
    shipment: Object,
});
</script>
