<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="New Expense Category" subtitle="Create a category for expenses" />

        <form @submit.prevent="submit" class="max-w-lg">
            <div class="card rounded-2xl p-6 space-y-5">
                <div>
                    <label class="form-label">Name <span class="text-red-500">*</span></label>
                    <input v-model="form.name" type="text" class="form-input" placeholder="e.g. Transport, Utilities" required />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea v-model="form.description" rows="3" class="form-input" placeholder="Optional description"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <Link :href="route('expense-categories.index')" class="btn btn-outline">Cancel</Link>
                <button type="submit" :disabled="form.processing" class="btn btn-accent">
                    {{ form.processing ? 'Saving...' : 'Create Category' }}
                </button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(route('expense-categories.store'));
};
</script>
