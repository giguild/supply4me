<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Edit Product" />

        <form @submit.prevent="submit">
            <div class="card p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">SKU</label>
                            <div class="flex gap-2">
                                <input v-model="form.sku" type="text" class="form-input flex-1" />
                                <button type="button" @click="generateSku" class="px-4 py-2 text-sm font-semibold rounded-lg border border-accent text-accent hover:bg-accent hover:text-white transition-colors whitespace-nowrap">Generate</button>
                            </div>
                            <p v-if="form.errors.sku" class="text-red-500 text-xs mt-1">{{ form.errors.sku }}</p>
                        </div>
                        <div>
                            <label class="form-label">Barcode</label>
                            <input v-model="form.barcode" type="text" class="form-input" />
                            <p v-if="form.errors.barcode" class="text-red-500 text-xs mt-1">{{ form.errors.barcode }}</p>
                        </div>
                        <div>
                            <label class="form-label">Name *</label>
                            <input v-model="form.name" type="text" class="form-input" />
                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label class="form-label">Description</label>
                            <textarea v-model="form.description" rows="3" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Category</label>
                            <select v-model="form.category_id" class="form-input">
                                <option value="">Select category</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Brand</label>
                            <select v-model="form.brand_id" class="form-input">
                                <option value="">Select brand</option>
                                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Unit</label>
                            <select v-model="form.unit_id" class="form-input">
                                <option value="">Select unit</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">{{ unit.name }} ({{ unit.short_name }})</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="form-label">Product Type</label>
                            <select v-model="form.product_type" class="form-input">
                                <option value="">Select type</option>
                                <option value="standard">Standard</option>
                                <option value="variant">Variant</option>
                                <option value="bundle">Bundle</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Cost Price *</label>
                            <input v-model="form.cost_price" type="number" step="0.01" min="0" class="form-input" />
                            <p v-if="form.errors.cost_price" class="text-red-500 text-xs mt-1">{{ form.errors.cost_price }}</p>
                        </div>
                        <div>
                            <label class="form-label">Selling Price *</label>
                            <input v-model="form.selling_price" type="number" step="0.01" min="0" class="form-input" />
                            <p v-if="form.errors.selling_price" class="text-red-500 text-xs mt-1">{{ form.errors.selling_price }}</p>
                        </div>
                        <div>
                            <label class="form-label">Minimum Price</label>
                            <input v-model="form.minimum_price" type="number" step="0.01" min="0" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Tax Rate (%)</label>
                            <input v-model="form.tax_rate" type="number" step="0.01" min="0" max="100" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Reorder Level</label>
                            <input v-model="form.reorder_level" type="number" min="0" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Reorder Quantity</label>
                            <input v-model="form.reorder_quantity" type="number" min="0" class="form-input" />
                        </div>
                        <div>
                            <label class="form-label">Minimum Order Quantity</label>
                            <input v-model="form.minimum_order_quantity" type="number" min="1" class="form-input" />
                            <p class="text-xs text-gray-500 mt-1">Minimum units a customer must order</p>
                        </div>
                        <div>
                            <label class="form-label">Maximum Order Quantity</label>
                            <div class="flex items-center gap-2">
                                <input v-model="form.maximum_order_quantity" type="number" min="1" class="form-input" :disabled="form.max_order_unlimited" placeholder="Leave empty for no limit" />
                                <label class="flex items-center gap-1 text-sm whitespace-nowrap">
                                    <input v-model="form.max_order_unlimited" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                                    <span class="text-gray-700 dark:text-gray-300">Unlimited</span>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Maximum units per order (leave empty or check Unlimited for no cap)</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6 mt-6 pt-6 border-t border-gray-100">
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_sellable" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                        <span class="text-gray-700 dark:text-gray-300">Sellable</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_purchasable" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                        <span class="text-gray-700 dark:text-gray-300">Purchasable</span>
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_stockable" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
                        <span class="text-gray-700 dark:text-gray-300">Stockable</span>
                    </label>
                </div>

                <div class="mt-4">
                    <label class="form-label">Status</label>
                    <select v-model="form.status" class="form-input w-48">
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="draft">Draft</option>
                        <option value="inactive">Inactive</option>
                        <option value="discontinued">Discontinued</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <Link :href="route('products.index')" class="btn btn-outline">Cancel</Link>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    {{ form.processing ? 'Updating...' : 'Update Product' }}
                </button>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    product: Object,
    categories: Array,
    brands: Array,
    units: Array,
});

const toast = useToast();

const form = useForm({
    name: props.product.name,
    sku: props.product.sku || '',
    barcode: props.product.barcode || '',
    description: props.product.description || '',
    category_id: props.product.category_id || '',
    brand_id: props.product.brand_id || '',
    unit_id: props.product.unit_id || '',
    product_type: props.product.product_type || '',
    cost_price: props.product.cost_price,
    selling_price: props.product.selling_price,
    minimum_price: props.product.minimum_price || '',
    tax_rate: props.product.tax_rate || '',
    reorder_level: props.product.reorder_level || '',
    reorder_quantity: props.product.reorder_quantity || '',
    minimum_order_quantity: props.product.minimum_order_quantity || 1,
    maximum_order_quantity: props.product.maximum_order_quantity || '',
    max_order_unlimited: !props.product.maximum_order_quantity,
    status: props.product.status || 'active',
    is_sellable: props.product.is_sellable,
    is_purchasable: props.product.is_purchasable,
    is_stockable: props.product.is_stockable,
});

const submit = () => {
    const data = { ...form.data() }
    data.maximum_order_quantity = data.max_order_unlimited ? null : data.maximum_order_quantity
    delete data.max_order_unlimited

    form.put(route('products.update', props.product.id), {
        data,
        onSuccess: () => toast.success('Product updated successfully'),
        onError: () => toast.error('Failed to update product'),
    });
};

const generateSku = () => {
    const prefix = form.name ? form.name.substring(0, 3).toUpperCase() : 'PRD';
    const random = Math.random().toString(36).substring(2, 8).toUpperCase();
    form.sku = `${prefix}-${random}`;
};
</script>
