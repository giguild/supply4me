<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Edit Category" subtitle="Update expense category" />

        <form @submit.prevent="submit" class="max-w-lg">
            <div class="card rounded-2xl p-6 space-y-5">
                <div>
                    <label class="form-label">Name <span class="text-red-500">*</span></label>
                    <input v-model="form.name" type="text" class="form-input" required />
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea v-model="form.description" rows="3" class="form-input"></textarea>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select v-model="form.status" class="form-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <Link :href="route('expense-categories.index')" class="btn btn-outline">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn btn-accent">
                    {{ form.processing ? 'Saving...' : 'Update Category' }}
                </button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';

const props = defineProps({
    category: Object,
});

const form = useForm({
    name: props.category.name,
    description: props.category.description ?? '',
    status: props.category.status,
});

const submit = () => {
    form.put(route('expense-categories.update', props.category));
};
</script>
