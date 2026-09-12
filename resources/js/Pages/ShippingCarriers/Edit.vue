<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader :title="`Edit ${carrier.name}`" subtitle="Update carrier details">
            <template #actions>
                <Link :href="route('shipping-carriers.index')" class="btn btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </Link>
            </template>
        </PageHeader>

        <div class="card rounded-2xl p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="form-label">Name *</label>
                        <input v-model="form.name" type="text" class="form-input" :class="{ 'border-red-500': form.errors.name }" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="form-label">Code *</label>
                        <input v-model="form.code" type="text" class="form-input" :class="{ 'border-red-500': form.errors.code }" />
                        <p v-if="form.errors.code" class="mt-1 text-sm text-red-500">{{ form.errors.code }}</p>
                    </div>

                    <div>
                        <label class="form-label">API Key</label>
                        <input v-model="form.api_key" type="text" class="form-input" />
                    </div>

                    <div>
                        <label class="form-label">API Secret</label>
                        <input v-model="form.api_secret" type="password" class="form-input" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label">Tracking URL</label>
                        <input v-model="form.tracking_url" type="url" class="form-input" placeholder="https://track.example.com/{tracking_number}" />
                    </div>

                    <div>
                        <label class="form-label">Status</label>
                        <select v-model="form.status" class="form-input">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <Link :href="route('shipping-carriers.index')" class="btn btn-outline">Cancel</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    carrier: Object,
});

const toast = useToast();

const form = useForm({
    name: props.carrier.name,
    code: props.carrier.code,
    api_key: props.carrier.api_key || '',
    api_secret: props.carrier.api_secret || '',
    tracking_url: props.carrier.tracking_url || '',
    status: props.carrier.status,
});

const submit = () => {
    form.put(route('shipping-carriers.update', props.carrier.id), {
        onSuccess: () => {
            toast.success('Carrier updated successfully.');
        },
    });
};
</script>