<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Product Units" />

        <div class="card p-6 mb-6">
            <form @submit.prevent="createUnit" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[140px]">
                    <label class="form-label">Name *</label>
                    <input v-model="createForm.name" type="text" class="form-input" placeholder="Unit name" />
                    <p v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</p>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="form-label">Short Name *</label>
                    <input v-model="createForm.short_name" type="text" class="form-input" placeholder="e.g. kg" />
                    <p v-if="createForm.errors.short_name" class="text-red-500 text-xs mt-1">{{ createForm.errors.short_name }}</p>
                </div>
                <div class="flex-1 min-w-[140px]">
                    <label class="form-label">Base Unit</label>
                    <select v-model="createForm.base_unit_id" class="form-input">
                        <option value="">None (Base Unit)</option>
                        <option v-for="unit in baseUnits" :key="unit.id" :value="unit.id">{{ unit.name }} ({{ unit.short_name }})</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[120px]">
                    <label class="form-label">Conversion Factor</label>
                    <input v-model="createForm.conversion_factor" type="number" step="0.0001" min="0" class="form-input" />
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
                            <th>Short Name</th>
                            <th>Base Unit</th>
                            <th>Conversion Factor</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="units.data.length === 0">
                            <td colspan="5">
                                <EmptyState title="No units yet" description="Add measurement units above to use with your products.">
                                    <template #icon>
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" /></svg>
                                    </template>
                                </EmptyState>
                            </td>
                        </tr>
                        <tr v-for="unit in units.data" :key="unit.id">
                            <td class="font-medium text-gray-900 dark:text-gray-100">
                                <template v-if="editingId === unit.id">
                                    <input v-model="editForm.name" type="text" class="form-input" />
                                </template>
                                <template v-else>{{ unit.name }}</template>
                            </td>
                            <td>
                                <template v-if="editingId === unit.id">
                                    <input v-model="editForm.short_name" type="text" class="form-input" />
                                </template>
                                <template v-else>{{ unit.short_name }}</template>
                            </td>
                            <td>{{ unit.baseUnit?.name || '-' }}</td>
                            <td>
                                <template v-if="editingId === unit.id">
                                    <input v-model="editForm.conversion_factor" type="number" step="0.0001" min="0" class="form-input" />
                                </template>
                                <template v-else>{{ unit.conversion_factor || '-' }}</template>
                            </td>
                            <td class="text-right" @click.stop>
                                <template v-if="editingId === unit.id">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="updateUnit" class="btn btn-accent btn-sm" :disabled="editForm.processing">Save</button>
                                        <button @click="cancelEdit" class="btn btn-outline btn-sm">Cancel</button>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="startEdit(unit)" class="btn btn-outline btn-sm">Edit</button>
                                        <button @click="deleteUnit(unit.id)" class="btn btn-danger btn-sm">Delete</button>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="units.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-gray-100">
                <span class="text-sm text-gray-500">Showing {{ units.from }}-{{ units.to }} of {{ units.total }}</span>
                <div class="flex gap-1">
                    <button v-for="link in units.links" :key="link.label" @click="link.url && goToPage(link.url)"
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
import EmptyState from '@/Components/UI/EmptyState.vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    units: Object,
    baseUnits: Array,
});

const toast = useToast();

const createForm = useForm({
    name: '',
    short_name: '',
    base_unit_id: '',
    conversion_factor: '',
});

const editingId = ref(null);
const editForm = useForm({
    name: '',
    short_name: '',
    conversion_factor: '',
});

const createUnit = () => {
    createForm.post(route('product-units.store'), {
        onSuccess: () => {
            toast.success('Unit created successfully');
            createForm.reset();
        },
        onError: () => toast.error('Failed to create unit'),
    });
};

const startEdit = (unit) => {
    editingId.value = unit.id;
    editForm.name = unit.name;
    editForm.short_name = unit.short_name;
    editForm.conversion_factor = unit.conversion_factor || '';
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const updateUnit = () => {
    editForm.put(route('product-units.update', editingId.value), {
        onSuccess: () => {
            toast.success('Unit updated successfully');
            editingId.value = null;
            editForm.reset();
        },
        onError: () => toast.error('Failed to update unit'),
    });
};

const deleteUnit = (id) => {
    if (confirm('Are you sure you want to delete this unit?')) {
        router.delete(route('product-units.destroy', id), {
            onSuccess: () => toast.success('Unit deleted successfully'),
            onError: () => toast.error('Failed to delete unit'),
        });
    }
};

const goToPage = (url) => {
    router.get(url, {}, { preserveState: true, replace: true });
};
</script>
