<template>
    <AppLayout :user="$page.props.auth.user">
        <PageHeader title="Featured Products" subtitle="Manage products shown on the storefront homepage">
            <template #actions>
                <button v-if="featured.length >= 8" disabled class="btn btn-outline opacity-50 cursor-not-allowed">
                    Max 8 reached
                </button>
                <button v-else @click="openAdd" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Product
                </button>
            </template>
        </PageHeader>

        <div class="card rounded-2xl p-6">
            <div v-if="featured.length === 0" class="text-center py-16">
                <div class="w-16 h-16 mx-auto bg-accent/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">No featured products yet</h3>
                <p class="text-sm text-gray-500 mb-4">Products you add will appear on the homepage carousel.</p>
                <button @click="openAdd" class="btn btn-primary">Add your first product</button>
            </div>

            <div v-else class="space-y-3">
                <div v-for="(item, index) in featured" :key="item.id"
                    class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 border border-[var(--color-border)] dark:border-gray-700 rounded-xl hover:shadow-md transition-all duration-200 group">
                    <!-- Position number -->
                    <div class="w-8 h-8 rounded-full bg-accent/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-sm font-bold text-accent">{{ index + 1 }}</span>
                    </div>

                    <!-- Product image placeholder -->
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-gradient-to-br from-accent-50 to-white dark:from-gray-700 dark:to-gray-800 flex items-center justify-center flex-shrink-0 border border-gray-100 dark:border-gray-600">
                        <span class="text-lg font-bold text-accent/30">{{ item.product?.name?.charAt(0) }}</span>
                    </div>

                    <!-- Product info -->
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ item.product?.name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ item.product?.sku }} · ₦{{ Number(item.product?.selling_price || 0).toLocaleString() }}
                        </p>
                    </div>

                    <!-- Status toggle -->
                    <button @click="toggleActive(item)" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200 flex-shrink-0"
                        :class="item.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400'">
                        {{ item.is_active ? 'Live' : 'Hidden' }}
                    </button>

                    <!-- Reorder arrows -->
                    <div class="flex flex-col gap-0.5 flex-shrink-0">
                        <button @click="moveUp(index)" :disabled="index === 0"
                            class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-20 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </button>
                        <button @click="moveDown(index)" :disabled="index === featured.length - 1"
                            class="p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-20 disabled:cursor-not-allowed transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    <!-- Remove -->
                    <button @click="remove(item)" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="showAddModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 pt-6 pb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Add to Featured</h3>
                    <p class="text-sm text-gray-500">Search and select a product to feature on the homepage.</p>
                </div>

                <div class="px-6">
                    <div class="relative">
                        <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input ref="searchInput" v-model="searchQuery" type="text" placeholder="Type product name or SKU..."
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm" />
                        <button v-if="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-2 max-h-64 overflow-y-auto">
                    <div v-if="filteredProducts.length === 0" class="py-8 text-center">
                        <p class="text-sm text-gray-500">{{ searchQuery ? 'No products match your search' : 'All products are already featured' }}</p>
                    </div>
                    <button v-for="p in filteredProducts" :key="p.id" @click="selectProduct(p)"
                        class="w-full flex items-center gap-3 p-3 rounded-xl hover:bg-accent/5 dark:hover:bg-accent/10 transition-colors text-left"
                        :class="{ 'bg-accent/10 ring-1 ring-accent': selectedProduct?.id === p.id }">
                        <div class="w-10 h-10 rounded-lg bg-accent-50 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-accent/40">{{ p.name?.charAt(0) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ p.name }}</p>
                            <p class="text-xs text-gray-500">{{ p.sku }} · ₦{{ Number(p.selling_price).toLocaleString() }}</p>
                        </div>
                        <svg v-if="selectedProduct?.id === p.id" class="w-5 h-5 text-accent flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex justify-end gap-3">
                    <button @click="showAddModal = false" class="btn btn-outline">Cancel</button>
                    <button v-if="featured.length >= 8" disabled class="btn btn-primary opacity-50 cursor-not-allowed">Max 8 reached</button>
                    <button v-else @click="addFeatured" :disabled="!selectedProduct" class="btn btn-primary">Add to Featured</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    featured: Array,
    products: Array,
});

const toast = useToast();
const showAddModal = ref(false);
const searchQuery = ref('');
const selectedProduct = ref(null);
const searchInput = ref(null);

const filteredProducts = computed(() => {
    const featuredIds = new Set(props.featured.map(f => f.product_id));
    const available = (props.products || []).filter(p => !featuredIds.has(p.id));
    if (!searchQuery.value) return available;
    const q = searchQuery.value.toLowerCase();
    return available.filter(p => p.name?.toLowerCase().includes(q) || p.sku?.toLowerCase().includes(q));
});

function openAdd() {
    searchQuery.value = '';
    selectedProduct.value = null;
    showAddModal.value = true;
    nextTick(() => searchInput.value?.focus());
}

function selectProduct(p) {
    selectedProduct.value = p;
}

function addFeatured() {
    if (!selectedProduct.value) return;
    router.post(route('featured-products.store'), {
        product_id: selectedProduct.value.id,
        sort_order: props.featured.length,
    }, {
        onSuccess: () => {
            toast.success('Added to featured.');
            showAddModal.value = false;
        },
    });
}

function moveUp(index) {
    if (index === 0) return;
    const arr = [...props.featured];
    [arr[index - 1], arr[index]] = [arr[index], arr[index - 1]];
    saveOrder(arr);
}

function moveDown(index) {
    if (index === props.featured.length - 1) return;
    const arr = [...props.featured];
    [arr[index], arr[index + 1]] = [arr[index + 1], arr[index]];
    saveOrder(arr);
}

function saveOrder(arr) {
    arr.forEach((item, i) => { item.sort_order = i; });
    // Update all sort orders in sequence
    const updates = arr.map((item, i) =>
        router.put(route('featured-products.update', item.id), { sort_order: i }, { preserveState: true, preserveScroll: true })
    );
    Promise.all(updates).then(() => toast.success('Order updated.'));
}

function toggleActive(item) {
    router.put(route('featured-products.update', item.id), {
        is_active: !item.is_active,
    }, {
        preserveState: true,
        onSuccess: () => toast.success(item.is_active ? 'Now live on homepage.' : 'Hidden from homepage.'),
    });
}

function remove(item) {
    if (!confirm(`Remove "${item.product?.name}" from featured?`)) return;
    router.delete(route('featured-products.destroy', item.id), {
        onSuccess: () => toast.success('Removed from featured.'),
    });
}
</script>