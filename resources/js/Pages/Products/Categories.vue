<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Product Categories" />

        <div class="card p-6 mb-6">
            <form @submit.prevent="createCategory" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[160px]">
                    <label class="form-label">Name *</label>
                    <input v-model="createForm.name" type="text" class="form-input" placeholder="Category name" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="form-label">Description</label>
                    <input v-model="createForm.description" type="text" class="form-input" placeholder="Description" />
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select v-model="createForm.status" class="form-input w-36">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-accent" :disabled="createForm.processing">
                    {{ createForm.processing ? 'Adding...' : 'Add' }}
                </button>
            </form>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="categories.data.length === 0">
                            <td colspan="4">
                                <EmptyState title="No categories yet" description="Add categories above to organize your products.">
                                    <template #icon>
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    </template>
                                </EmptyState>
                            </td>
                        </tr>
                        <tr v-for="category in categories.data" :key="category.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">
                                <template v-if="editingId === category.id">
                                    <input v-model="editForm.name" type="text" class="form-input" />
                                </template>
                                <template v-else>{{ category.name }}</template>
                            </td>
                            <td>
                                <template v-if="editingId === category.id">
                                    <input v-model="editForm.description" type="text" class="form-input" />
                                </template>
                                <template v-else>{{ category.description || '-' }}</template>
                            </td>
                            <td>
                                <template v-if="editingId === category.id">
                                    <select v-model="editForm.status" class="form-input w-32">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </template>
                                <template v-else>
                                    <StatusBadge :value="category.status || 'active'" />
                                </template>
                            </td>
                            <td class="text-right" @click.stop>
                                <template v-if="editingId === category.id">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="updateCategory" class="btn btn-accent btn-sm" :disabled="editForm.processing">Save</button>
                                        <button @click="cancelEdit" class="btn btn-outline btn-sm">Cancel</button>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="startEdit(category)" class="btn btn-outline btn-sm">Edit</button>
                                        <button @click="deleteCategory(category.id)" class="btn btn-danger btn-sm">Delete</button>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="categories.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                <span class="text-sm text-gray-500">Showing {{ categories.from }}-{{ categories.to }} of {{ categories.total }}</span>
                <div class="flex gap-1">
                    <button v-for="link in categories.links" :key="link.label" @click="link.url && goToPage(link.url)"
                        class="px-3 py-1 rounded-lg text-sm" :disabled="!link.url"
                        :class="link.active ? 'bg-gray-900 text-white' : 'hover:bg-gray-100'" v-html="link.label" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    categories: Object,
});

const toast = useToast();

const createForm = useForm({
    name: '',
    description: '',
    status: 'active',
});

const editingId = ref(null);
const editForm = useForm({
    name: '',
    description: '',
    status: 'active',
});

const createCategory = () => {
    createForm.post(route('product-categories.store'), {
        onSuccess: () => {
            toast.success('Category created successfully');
            createForm.reset();
            createForm.status = 'active';
        },
        onError: () => toast.error('Failed to create category'),
    });
};

const startEdit = (category) => {
    editingId.value = category.id;
    editForm.name = category.name;
    editForm.description = category.description || '';
    editForm.status = category.status || 'active';
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const updateCategory = () => {
    editForm.put(route('product-categories.update', editingId.value), {
        onSuccess: () => {
            toast.success('Category updated successfully');
            editingId.value = null;
            editForm.reset();
        },
        onError: () => toast.error('Failed to update category'),
    });
};

const deleteCategory = (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        router.delete(route('product-categories.destroy', id), {
            onSuccess: () => toast.success('Category deleted successfully'),
            onError: () => toast.error('Failed to delete category'),
        });
    }
};

const goToPage = (url) => {
    router.get(url, {}, { preserveState: true, replace: true });
};
</script>
