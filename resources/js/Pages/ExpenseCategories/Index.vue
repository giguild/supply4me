<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Expense Categories" subtitle="Manage expense categories">
            <template #actions>
                <Link :href="route('expense-categories.create')" class="btn btn-accent">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Category
                </Link>
            </template>
        </PageHeader>

        <div class="card rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Expenses</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cat in categories" :key="cat.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">{{ cat.name }}</td>
                            <td class="text-gray-600 dark:text-gray-400">{{ cat.description ?? '-' }}</td>
                            <td>{{ cat.expenses_count }}</td>
                            <td>
                                <StatusBadge :value="cat.status" :variant="cat.status === 'active' ? 'success' : 'secondary'" />
                            </td>
                            <td class="text-right space-x-3">
                                <Link :href="route('expense-categories.edit', cat)" class="text-sm text-[#9F5124] hover:underline">Edit</Link>
                                <button @click="confirmDelete(cat)" class="text-sm text-red-600 hover:underline">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="!categories.length">
                            <td colspan="5" class="text-center py-8 text-gray-500">No categories yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';

defineProps({
    categories: Array,
});

const confirmDelete = (cat) => {
    if (confirm(`Delete category "${cat.name}"?`)) {
        router.delete(route('expense-categories.destroy', cat));
    }
};
</script>
