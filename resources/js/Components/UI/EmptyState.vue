<template>
    <div class="text-center py-12">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
            <slot name="icon">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </slot>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">{{ title }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ description }}</p>
        <slot name="action" />
    </div>
</template>

<script setup>
defineProps({ title: String, description: String });
</script>
