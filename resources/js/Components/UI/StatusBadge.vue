<template>
    <span :class="['badge', variantClass]">{{ label || value }}</span>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
    value: String,
    label: String,
    variant: { type: String, default: 'gray' },
});

const COLOR_MAP = {
    active: 'success',
    approved: 'success',
    completed: 'success',
    delivered: 'success',
    paid: 'success',
    verified: 'success',
    fulfilled: 'success',
    in_stock: 'success',
    good: 'success',
    success: 'success',

    pending: 'warning',
    pending_approval: 'warning',
    partial: 'warning',
    partial_delivery: 'warning',
    on_hold: 'warning',
    low_stock: 'warning',
    unpaid: 'warning',
    packing: 'warning',

    cancelled: 'danger',
    voided: 'danger',
    void: 'danger',
    overdue: 'danger',
    rejected: 'danger',
    inactive: 'danger',
    out_of_stock: 'danger',
    expired: 'danger',
    suspended: 'danger',
    blocked: 'danger',
    blacklisted: 'danger',
    failed: 'danger',
    return: 'danger',

    draft: 'info',
    processing: 'info',
    sent: 'info',
    in_progress: 'info',
    assigned: 'info',
    incoming: 'info',
    planned: 'info',
    viewed: 'info',

    confirmed: 'purple',
    overpaid: 'purple',
    refunded: 'purple',
    returned: 'purple',

    shipped: 'teal',
    in_transit: 'teal',
    out_for_delivery: 'teal',
    picked: 'teal',
    picked_up: 'teal',

    unfulfilled: 'orange',
    discontinued: 'orange',
    damaged: 'orange',

    packed: 'indigo',
    transfer: 'indigo',

    on_leave: 'pink',
    cycle_count: 'pink',
    physical_count: 'pink',
};

const variantClass = computed(() => {
    const color = COLOR_MAP[props.value] || (props.variant !== 'gray' ? props.variant : null) || 'gray';
    return `badge-${color}`;
});
</script>
