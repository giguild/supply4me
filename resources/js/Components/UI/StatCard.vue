<template>
    <div class="stat-card">
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ label }}</p>
        <div class="flex items-baseline gap-2">
            <span class="text-2xl font-bold">{{ prefix }}{{ formattedValue }}</span>
            <span v-if="suffix" class="text-lg font-semibold text-gray-400 dark:text-gray-500">{{ suffix }}</span>
        </div>
        <div class="flex items-center gap-2 mt-2">
            <span v-if="trend !== null" :class="trend >= 0 ? 'trend-up' : 'trend-down'">
                {{ trend >= 0 ? '↗' : '↘' }} {{ Math.abs(trend) }}%
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500">{{ subtitle }}</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
    label: String,
    value: [Number, String],
    prefix: { type: String, default: '' },
    suffix: { type: String, default: '' },
    trend: { type: Number, default: null },
    subtitle: { type: String, default: '' },
    format: { type: Boolean, default: true },
});
const formattedValue = computed(() => {
    if (!props.format || typeof props.value === 'string') return props.value;
    return props.value?.toLocaleString() ?? '0';
});
</script>
