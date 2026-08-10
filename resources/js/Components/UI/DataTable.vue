<template>
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th v-for="col in columns" :key="col.key">{{ col.label }}</th>
                        <th v-if="$slots.actions" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, i) in data" :key="i" class="cursor-pointer" @click="$emit('rowClick', row)">
                        <td v-for="col in columns" :key="col.key">
                            <slot :name="'cell-' + col.key" :row="row" :value="row[col.key]">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="text-right" @click.stop>
                            <slot name="actions" :row="row" />
                        </td>
                    </tr>
                    <tr v-if="data.length === 0">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)">
                            <slot name="empty">
                                <div class="text-center py-8 text-gray-400 dark:text-gray-500">No data found</div>
                            </slot>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="meta && meta.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">Showing {{ meta.from }}-{{ meta.to }} of {{ meta.total }}</span>
            <div class="flex gap-1">
                <button v-for="p in visiblePages" :key="p" @click="$emit('page', p)"
                    :class="['px-3 py-1 rounded-lg text-sm', p === meta.current_page ? 'bg-gray-900 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700']">
                    {{ p }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({
    columns: Array,
    data: { type: Array, default: () => [] },
    meta: Object,
});
defineEmits(['rowClick', 'page']);
const visiblePages = computed(() => {
    if (!props.meta) return [];
    const { current_page, last_page } = props.meta;
    const pages = [];
    for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) pages.push(i);
    return pages;
});
</script>
