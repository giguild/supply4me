<template>
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ title }}</h1>
            <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ subtitle }}</p>
        </div>
        <div class="flex items-center gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup>
defineProps({ title: String, subtitle: String });
</script>
