<template>
  <StorefrontLayout :cartCount="cartCount" :customer="customer" currentPage="account">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Customer Info -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
        <h1 class="text-2xl font-bold text-[var(--color-text)] mb-4">My Account</h1>
        <div class="grid grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-[var(--color-text-secondary)]">Name</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.name }}</p>
          </div>
          <div>
            <span class="text-[var(--color-text-secondary)]">Email</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.email }}</p>
          </div>
          <div>
            <span class="text-[var(--color-text-secondary)]">Phone</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.phone || 'N/A' }}</p>
          </div>
          <div>
            <span class="text-[var(--color-text-secondary)]">Customer #</span>
            <p class="font-medium text-[var(--color-text)]">{{ customer.customer_number }}</p>
          </div>
        </div>
      </div>

      <!-- Shipping Addresses -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 mb-6 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-bold text-[var(--color-text)]">Shipping Addresses</h2>
          <button @click="openAddModal" class="text-sm bg-accent text-white px-4 py-2 rounded-full hover:bg-accent-hover transition-colors">
            + Add Address
          </button>
        </div>

        <div v-if="addresses.length === 0" class="text-center py-8">
          <p class="text-[var(--color-text-secondary)]">No shipping addresses yet.</p>
          <p class="text-xs text-[var(--color-text-secondary)] mt-1">Add a shipping address for faster checkout.</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-for="address in addresses" :key="address.id" class="border border-[var(--color-border)] rounded-xl p-4 relative dark:border-gray-600">
            <div class="flex items-center justify-between mb-2">
              <h3 class="font-semibold text-[var(--color-text)]">{{ address.label }}</h3>
              <span v-if="address.is_default" class="text-xs bg-accent/10 text-accent px-2 py-0.5 rounded-full font-medium">Default</span>
            </div>
            <p class="text-sm text-[var(--color-text-secondary)]">{{ address.address_line_1 }}</p>
            <p v-if="address.address_line_2" class="text-sm text-[var(--color-text-secondary)]">{{ address.address_line_2 }}</p>
            <p class="text-sm text-[var(--color-text-secondary)]">{{ [address.city, address.state, address.postal_code].filter(Boolean).join(', ') }}</p>
            <p v-if="address.country" class="text-sm text-[var(--color-text-secondary)]">{{ address.country }}</p>
            <p v-if="address.delivery_instructions" class="text-xs text-[var(--color-text-secondary)] mt-2 italic">"{{ address.delivery_instructions }}"</p>
            <div class="flex gap-2 mt-3">
              <button @click="openEditModal(address)" class="text-xs border border-[var(--color-border)] text-[var(--color-text)] px-3 py-1 rounded-full hover:border-accent transition-colors dark:border-gray-600">Edit</button>
              <button v-if="!address.is_default" @click="setDefault(address)" class="text-xs border border-[var(--color-border)] text-[var(--color-text)] px-3 py-1 rounded-full hover:border-accent transition-colors dark:border-gray-600">Set Default</button>
              <button @click="deleteAddress(address)" class="text-xs border border-red-300 text-red-600 px-3 py-1 rounded-full hover:bg-red-50 transition-colors dark:border-red-700 dark:text-red-400">Delete</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Orders -->
      <div class="bg-white rounded-2xl border border-[var(--color-border)] p-6 dark:bg-gray-800 dark:border-gray-700">
        <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">My Orders</h2>

        <div v-if="orders.data?.length === 0" class="text-center py-8">
          <p class="text-[var(--color-text-secondary)]">No orders yet.</p>
          <a href="/" class="text-accent font-semibold hover:text-accent-hover mt-2 inline-block">Start Shopping</a>
        </div>

        <div v-else class="space-y-4">
          <div v-for="order in orders.data" :key="order.id" class="border border-[var(--color-border)] rounded-xl p-4 dark:border-gray-600">
            <div class="flex items-center justify-between mb-2">
              <div>
                <h3 class="font-semibold text-[var(--color-text)]">Order #{{ order.order_number }}</h3>
                <p class="text-xs text-[var(--color-text-secondary)]">{{ new Date(order.created_at).toLocaleDateString() }}</p>
              </div>
              <span class="badge" :class="order.status === 'completed' ? 'badge-success' : order.status === 'cancelled' ? 'badge-danger' : 'badge-warning'">
                {{ order.status }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="font-bold text-accent">N{{ Number(order.total_amount).toLocaleString() }}</span>
              <div class="flex gap-2">
                <a v-if="order.invoice && order.invoice.status !== 'paid'" :href="`/payment/${order.invoice.id}`" class="text-xs bg-accent text-white px-3 py-1 rounded-full hover:bg-accent-hover transition-colors">Upload Payment</a>
                <span v-if="order.invoice?.status === 'paid'" class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full dark:bg-green-900/30 dark:text-green-400">Paid</span>
                <a :href="`/order-confirmation/${order.id}`" class="text-xs border border-[var(--color-border)] text-[var(--color-text)] px-3 py-1 rounded-full hover:border-accent transition-colors dark:border-gray-600">View</a>
              </div>
            </div>
            <div v-if="order.invoice" class="mt-2 text-xs text-[var(--color-text-secondary)]">
              Invoice: {{ order.invoice.invoice_number }} &middot;
              Paid: N{{ Number(order.invoice.paid_amount || 0).toLocaleString() }} &middot;
              Due: <span :class="order.invoice.due_amount > 0 ? 'text-accent font-semibold' : 'text-green-600 dark:text-green-400'">N{{ Number(order.invoice.due_amount || 0).toLocaleString() }}</span>
            </div>
          </div>
        </div>

        <div v-if="orders.last_page > 1" class="flex justify-center gap-2 mt-6">
          <button
            v-for="page in orders.last_page"
            :key="page"
            @click="goToPage(page)"
            class="px-3 py-1 rounded-full text-sm font-medium transition-colors cursor-pointer"
            :class="page === orders.current_page ? 'bg-accent text-white' : 'bg-white border border-[var(--color-border)] text-[var(--color-text)] hover:border-accent dark:bg-gray-800 dark:border-gray-600 dark:text-white'"
          >
            {{ page }}
          </button>
        </div>
      </div>
    </div>

    <!-- Address Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeModal">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg mx-4 p-6">
        <h3 class="text-lg font-bold text-[var(--color-text)] mb-4">{{ editingAddress ? 'Edit Address' : 'Add Shipping Address' }}</h3>
        <form @submit.prevent="saveAddress" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Label *</label>
            <input v-model="addressForm.label" type="text" placeholder="e.g. Home, Office, Warehouse" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            <p v-if="addressForm.errors.label" class="text-red-500 text-xs mt-1">{{ addressForm.errors.label }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Address Line 1 *</label>
            <input v-model="addressForm.address_line_1" type="text" placeholder="Street address" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            <p v-if="addressForm.errors.address_line_1" class="text-red-500 text-xs mt-1">{{ addressForm.errors.address_line_1 }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Address Line 2</label>
            <input v-model="addressForm.address_line_2" type="text" placeholder="Apartment, suite, etc. (optional)" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">City</label>
              <input v-model="addressForm.city" type="text" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">State</label>
              <input v-model="addressForm.state" type="text" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Postal Code</label>
              <input v-model="addressForm.postal_code" type="text" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
            <div>
              <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Country</label>
              <input v-model="addressForm.country" type="text" maxlength="2" placeholder="NG" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Delivery Instructions</label>
            <textarea v-model="addressForm.delivery_instructions" rows="2" placeholder="Optional notes for delivery" class="w-full px-4 py-2 rounded-lg border border-[var(--color-border)] bg-white text-[var(--color-text)] focus:outline-none focus:ring-2 focus:ring-accent dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
          </div>
          <label class="flex items-center gap-2">
            <input v-model="addressForm.is_default" type="checkbox" class="rounded border-gray-300 text-accent focus:ring-accent" />
            <span class="text-sm text-[var(--color-text)]">Set as default shipping address</span>
          </label>
          <div class="flex gap-3 pt-2">
            <button type="button" @click="closeModal" class="flex-1 px-4 py-2 rounded-lg border border-[var(--color-border)] text-[var(--color-text)] hover:bg-gray-50 transition-colors dark:border-gray-600 dark:hover:bg-gray-700">Cancel</button>
            <button type="submit" :disabled="addressForm.processing" class="flex-1 px-4 py-2 rounded-lg bg-accent text-white hover:bg-accent-hover transition-colors disabled:opacity-50">
              {{ addressForm.processing ? 'Saving...' : (editingAddress ? 'Update' : 'Save') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </StorefrontLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import StorefrontLayout from '@/Components/Layout/StorefrontLayout.vue'

const props = defineProps({
  customer: Object,
  orders: Object,
  addresses: { type: Array, default: () => [] },
  cartCount: { type: Number, default: 0 },
})

const showModal = ref(false)
const editingAddress = ref(null)

const addressForm = useForm({
  label: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: 'NG',
  delivery_instructions: '',
  is_default: false,
})

function openAddModal() {
  editingAddress.value = null
  addressForm.reset()
  addressForm.country = 'NG'
  showModal.value = true
}

function openEditModal(address) {
  editingAddress.value = address
  addressForm.label = address.label
  addressForm.address_line_1 = address.address_line_1
  addressForm.address_line_2 = address.address_line_2 || ''
  addressForm.city = address.city || ''
  addressForm.state = address.state || ''
  addressForm.postal_code = address.postal_code || ''
  addressForm.country = address.country || 'NG'
  addressForm.delivery_instructions = address.delivery_instructions || ''
  addressForm.is_default = address.is_default
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingAddress.value = null
  addressForm.reset()
  addressForm.clearErrors()
}

function saveAddress() {
  if (editingAddress.value) {
    addressForm.put(`/account/addresses/${editingAddress.value.id}`, {
      onSuccess: () => closeModal(),
    })
  } else {
    addressForm.post('/account/addresses', {
      onSuccess: () => closeModal(),
    })
  }
}

function setDefault(address) {
  addressForm.is_default = true
  addressForm.put(`/account/addresses/${address.id}`)
  addressForm.is_default = false
}

function deleteAddress(address) {
  if (confirm('Are you sure you want to delete this address?')) {
    useForm().delete(`/account/addresses/${address.id}`)
  }
}

function goToPage(page) {
  router.get('/account', { page }, { preserveState: true, replace: true })
}
</script>
