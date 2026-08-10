<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Products">
            <template #actions>
                <Link :href="route('products.create')" class="btn btn-accent">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Product
                </Link>
            </template>
        </PageHeader>

        <div class="flex flex-wrap items-center gap-3 mb-6">
            <div class="w-64">
                <SearchInput v-model="search" @input="debouncedSearch" />
            </div>
            <select v-model="filterCategory" class="form-input w-48" @change="applyFilters">
                <option value="">All Categories</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <select v-model="filterBrand" class="form-input w-48" @change="applyFilters">
                <option value="">All Brands</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
            </select>
            <select v-model="filterStatus" class="form-input w-40" @change="applyFilters">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="inactive">Inactive</option>
                <option value="discontinued">Discontinued</option>
            </select>
        </div>

        <div class="hidden md:block">
            <DataTable :columns="columns" :data="products.data" :meta="meta" @page="goToPage">
                <template #cell-sku="{ row }">
                    {{ row.sku || '-' }}
                </template>
                <template #cell-name="{ row }">
                    <Link :href="route('products.show', row.id)" class="font-medium text-accent hover:underline">
                        {{ row.name }}
                    </Link>
                </template>
                <template #cell-category="{ row }">
                    {{ row.category?.name || '-' }}
                </template>
                <template #cell-brand="{ row }">
                    {{ row.brand?.name || '-' }}
                </template>
                <template #cell-selling_price="{ row }">
                    {{ formatCurrency(row.selling_price) }}
                </template>
                <template #cell-status="{ row }">
                    <StatusBadge :value="row.status || 'active'" />
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-end gap-2">
                        <Link :href="route('products.show', row.id)" class="btn btn-outline btn-sm">View</Link>
                        <Link :href="route('products.edit', row.id)" class="btn btn-outline btn-sm">Edit</Link>
                        <button @click="deleteProduct(row.id)" class="btn btn-danger btn-sm">Delete</button>
                    </div>
                </template>

                <template #empty>
                    <EmptyState title="No products found" description="Try adjusting your filters or add a new product.">
                        <template #icon>
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </template>
                        <template #action>
                            <Link :href="route('products.create')" class="btn btn-accent">Add Product</Link>
                        </template>
                    </EmptyState>
                </template>
            </DataTable>
        </div>

        <div class="md:hidden space-y-3">
            <EmptyState v-if="products.data.length === 0" title="No products found" description="Try adjusting your filters or add a new product.">
                <template #action>
                    <Link :href="route('products.create')" class="btn btn-accent">Add Product</Link>
                </template>
            </EmptyState>
            <div v-for="product in products.data" :key="product.id" class="card p-4">
                <div class="flex items-start justify-between mb-2">
                    <Link :href="route('products.show', product.id)" class="font-semibold text-gray-900 hover:text-accent">
                        {{ product.name }}
                    </Link>
                    <StatusBadge :value="product.status || 'active'" />
                </div>
                <p class="text-sm text-gray-500 mb-1">SKU: {{ product.sku || '-' }}</p>
                <p class="text-sm text-gray-500 mb-1">Category: {{ product.category?.name || '-' }}</p>
                <p class="text-sm text-gray-500 mb-3">Brand: {{ product.brand?.name || '-' }}</p>
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-gray-900">{{ formatCurrency(product.selling_price) }}</span>
                    <div class="flex gap-2">
                        <Link :href="route('products.edit', product.id)" class="btn btn-outline btn-sm">Edit</Link>
                        <button @click="deleteProduct(product.id)" class="btn btn-danger btn-sm">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="meta && meta.last_page > 1" class="md:hidden flex items-center justify-between mt-4">
            <span class="text-sm text-gray-500">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
            <div class="flex gap-2">
                <button @click="goToPage(meta.current_page - 1)" :disabled="meta.current_page <= 1" class="btn btn-outline btn-sm">Prev</button>
                <button @click="goToPage(meta.current_page + 1)" :disabled="meta.current_page >= meta.last_page" class="btn btn-outline btn-sm">Next</button>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import DataTable from '@/Components/UI/DataTable.vue';
import StatCard from '@/Components/UI/StatCard.vue';
import StatusBadge from '@/Components/UI/StatusBadge.vue';
import SearchInput from '@/Components/UI/SearchInput.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    products: Object,
    categories: Array,
    brands: Array,
    filters: Object,
});

const toast = useToast();

const search = ref(props.filters?.search || '');
const filterCategory = ref(props.filters?.category_id || '');
const filterBrand = ref(props.filters?.brand_id || '');
const filterStatus = ref(props.filters?.status || '');

const columns = [
    { key: 'sku', label: 'SKU' },
    { key: 'name', label: 'Name' },
    { key: 'category', label: 'Category' },
    { key: 'brand', label: 'Brand' },
    { key: 'selling_price', label: 'Selling Price' },
    { key: 'status', label: 'Status' },
];

const meta = computed(() => ({
    current_page: props.products.current_page,
    last_page: props.products.last_page,
    from: props.products.from,
    to: props.products.to,
    total: props.products.total,
}));

let searchTimeout = null;

const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(), 300);
};

const applyFilters = () => {
    router.get(route('products.index'), {
        search: search.value,
        category_id: filterCategory.value,
        brand_id: filterBrand.value,
        status: filterStatus.value,
    }, { preserveState: true, replace: true });
};

const goToPage = (page) => {
    if (page < 1 || page > props.products.last_page) return;
    router.get(route('products.index'), {
        page,
        search: search.value,
        category_id: filterCategory.value,
        brand_id: filterBrand.value,
        status: filterStatus.value,
    }, { preserveState: true, replace: true });
};

const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(route('products.destroy', id), {
            onSuccess: () => toast.success('Product deleted successfully'),
            onError: () => toast.error('Failed to delete product'),
        });
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value || 0);
};
</script>
