<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="`Delivery ${delivery.delivery_number}`" subtitle="Delivery details and driver information">
            <template #actions>
                <Link :href="route('deliveries.index')" class="btn btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </Link>
            </template>
        </PageHeader>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4">Delivery Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Delivery Number</p>
                        <p class="font-medium">{{ delivery.delivery_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                        <StatusBadge :value="delivery.status" :label="delivery.status?.replace('_', ' ')" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Order</p>
                        <p class="font-medium">{{ delivery.order?.order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Driver</p>
                        <p class="font-medium">{{ delivery.driver?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Route</p>
                        <p class="font-medium">{{ delivery.route?.name || 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Scheduled Date</p>
                        <p class="font-medium">{{ delivery.scheduled_date }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Actual Delivery</p>
                        <p class="font-medium">{{ delivery.actual_delivery || 'N/A' }}</p>
                    </div>
                </div>
                <div v-if="delivery.notes" class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                    <p class="font-medium">{{ delivery.notes }}</p>
                </div>
            </div>

            <div class="card rounded-2xl p-6">
                <h3 class="text-lg font-semibold mb-4">Driver Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                        <p class="font-medium">{{ delivery.driver?.name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                        <p class="font-medium">{{ delivery.driver?.phone }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Vehicle</p>
                        <p class="font-medium">{{ delivery.driver?.vehicle_type }} - {{ delivery.driver?.vehicle_registration }}</p>
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
    delivery: Object,
});
</script>
