<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Product Brands" />

        <div class="card p-6 mb-6">
            <form @submit.prevent="createBrand" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[160px]">
                    <label class="form-label">Name *</label>
                    <input v-model="createForm.name" type="text" class="form-input" placeholder="Brand name" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
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
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="brands.data.length === 0">
                            <td colspan="3">
                                <EmptyState title="No brands yet" description="Add brands above to organize your products.">
                                    <template #icon>
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                                    </template>
                                </EmptyState>
                            </td>
                        </tr>
                        <tr v-for="brand in brands.data" :key="brand.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">
                                <template v-if="editingId === brand.id">
                                    <input v-model="editForm.name" type="text" class="form-input" />
                                </template>
                                <template v-else>{{ brand.name }}</template>
                            </td>
                            <td>
                                <template v-if="editingId === brand.id">
                                    <select v-model="editForm.status" class="form-input w-32">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </template>
                                <template v-else>
                                    <StatusBadge :value="brand.status || 'active'" />
                                </template>
                            </td>
                            <td class="text-right" @click.stop>
                                <template v-if="editingId === brand.id">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="updateBrand" class="btn btn-accent btn-sm" :disabled="editForm.processing">Save</button>
                                        <button @click="cancelEdit" class="btn btn-outline btn-sm">Cancel</button>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="startEdit(brand)" class="btn btn-outline btn-sm">Edit</button>
                                        <button @click="deleteBrand(brand.id)" class="btn btn-danger btn-sm">Delete</button>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="brands.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                <span class="text-sm text-gray-500">Showing {{ brands.from }}-{{ brands.to }} of {{ brands.total }}</span>
                <div class="flex gap-1">
                    <button v-for="link in brands.links" :key="link.label" @click="link.url && goToPage(link.url)"
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
    brands: Object,
});

const toast = useToast();

const createForm = useForm({
    name: '',
    status: 'active',
});

const editingId = ref(null);
const editForm = useForm({
    name: '',
    status: 'active',
});

const createBrand = () => {
    createForm.post(route('product-brands.store'), {
        onSuccess: () => {
            toast.success('Brand created successfully');
            createForm.reset();
            createForm.status = 'active';
        },
        onError: () => toast.error('Failed to create brand'),
    });
};

const startEdit = (brand) => {
    editingId.value = brand.id;
    editForm.name = brand.name;
    editForm.status = brand.status || 'active';
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const updateBrand = () => {
    editForm.put(route('product-brands.update', editingId.value), {
        onSuccess: () => {
            toast.success('Brand updated successfully');
            editingId.value = null;
            editForm.reset();
        },
        onError: () => toast.error('Failed to update brand'),
    });
};

const deleteBrand = (id) => {
    if (confirm('Are you sure you want to delete this brand?')) {
        router.delete(route('product-brands.destroy', id), {
            onSuccess: () => toast.success('Brand deleted successfully'),
            onError: () => toast.error('Failed to delete brand'),
        });
    }
};

const goToPage = (url) => {
    router.get(url, {}, { preserveState: true, replace: true });
};
</script>
