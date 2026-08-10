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
const variantClass = computed(() => {
    const map = {
        active: 'badge-success', completed: 'badge-success', paid: 'badge-success', delivered: 'badge-success', confirmed: 'badge-success',
        pending: 'badge-warning', processing: 'badge-warning', draft: 'badge-info', sent: 'badge-info', in_transit: 'badge-info',
        cancelled: 'badge-danger', voided: 'badge-danger', overdue: 'badge-danger', rejected: 'badge-danger', inactive: 'badge-danger',
        out_of_stock: 'badge-danger', low_stock: 'badge-warning',
    };
    return `badge-${props.variant || map[props.value] || 'gray'}`;
});
</script>
