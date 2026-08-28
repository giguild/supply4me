<template>
    <div class="card">
        <!-- Mobile card view -->
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
            <div v-if="data.length === 0" class="text-center py-8 text-gray-400 dark:text-gray-500">
                <slot name="empty">No data found</slot>
            </div>
            <div v-for="(row, i) in data" :key="i" class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer" @click="$emit('rowClick', row)">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <div v-for="col in mobileColumns" :key="col.key" class="flex items-baseline gap-2">
                            <span class="text-xs font-medium text-gray-400 dark:text-gray-500 shrink-0">{{ col.label }}:</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100 min-w-0 truncate">
                                <slot :name="'cell-' + col.key" :row="row" :value="row[col.key]">
                                    {{ row[col.key] }}
                                </slot>
                            </span>
                        </div>
                    </div>
                    <div v-if="$slots.actions" class="shrink-0" @click.stop>
                        <slot name="actions" :row="row" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop table view -->
        <div class="hidden md:block overflow-x-auto">
            <table class="data-table w-full">
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

        <div v-if="meta && meta.total > 0" class="flex items-center justify-between px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            <span class="text-sm text-gray-500 dark:text-gray-400">Showing {{ meta.from }}-{{ meta.to }} of {{ meta.total }}</span>
            <div v-if="meta.last_page > 1" class="flex gap-1">
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
    mobileColumns: { type: Array, default: null },
});
defineEmits(['rowClick', 'page']);

const mobileColumns = computed(() => {
    if (props.mobileColumns) return props.mobileColumns;
    return props.columns.slice(0, 3);
});

const visiblePages = computed(() => {
    if (!props.meta) return [];
    const { current_page, last_page } = props.meta;
    const pages = [];
    for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) pages.push(i);
    return pages;
});
</script>
