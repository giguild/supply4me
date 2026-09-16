<template>
  <AppLayout :user="$page.props.auth.user">
    <!-- Hero / Welcome -->
    <div class="relative overflow-hidden rounded-2xl mb-6 animate-fade-in"
      :style="{ background: 'var(--surface-strong)', border: '1px solid var(--border)', boxShadow: 'var(--shadow-md)' }">
      <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 p-6 lg:p-8">
        <div>
          <p class="text-sm font-medium mb-1" :style="{ color: 'var(--text-muted)' }">Welcome back,</p>
          <h1 class="text-2xl lg:text-3xl font-bold mb-1" :style="{ color: 'var(--text)' }">
            {{ user?.name }} <span class="inline-block animate-bounce">👋</span>
          </h1>
          <p class="text-sm" :style="{ color: 'var(--text-secondary)' }">Here's what's happening with your business today.</p>
        </div>
        <div class="text-right hidden lg:block">
          <p class="text-sm font-medium italic" :style="{ color: 'var(--brand)' }">"Moving business forward, together."</p>
          <p class="text-xs mt-1" :style="{ color: 'var(--text-muted)' }">{{ currentDate }}</p>
        </div>
      </div>
      <!-- Decorative gradient -->
      <div class="absolute top-0 right-0 w-64 h-64 rounded-full opacity-20 blur-3xl pointer-events-none" :style="{ background: 'var(--brand)' }"></div>
    </div>

    <!-- KPI Row -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
      <div v-for="(kpi, i) in kpis" :key="kpi.label"
        class="kpi-card animate-fade-in"
        :class="`stagger-${i + 1}`"
        :style="{ background: 'var(--surface)', border: '1px solid var(--border)', borderRadius: 'var(--radius)', boxShadow: 'var(--shadow-sm)' }">
        <div class="flex items-start justify-between mb-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center" :style="{ background: kpi.iconBg, color: kpi.iconColor }">
            <span v-html="kpi.icon" class="w-5 h-5"></span>
          </div>
          <span v-if="kpi.trend !== null" class="badge" :class="kpi.trend >= 0 ? 'badge-success' : 'badge-danger'">
            {{ kpi.trend >= 0 ? '+' : '' }}{{ kpi.trend }}%
          </span>
        </div>
        <p class="text-xs font-medium mb-0.5" :style="{ color: 'var(--text-muted)' }">{{ kpi.label }}</p>
        <p class="text-xl font-bold" :style="{ color: 'var(--text)' }">{{ kpi.prefix }}{{ formatNumber(kpi.value) }}</p>
        <p class="text-[11px] mt-1" :style="{ color: 'var(--text-muted)' }">{{ kpi.subtitle }}</p>
      </div>
    </div>

    <!-- Analytics Row: Sales Overview + Order Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <!-- Sales Overview Chart -->
      <div class="lg:col-span-2 card p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" :style="{ background: 'var(--brand-soft)', color: 'var(--brand)' }">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
              <h3 class="text-sm font-semibold" :style="{ color: 'var(--text)' }">Sales Overview</h3>
              <p class="text-xs" :style="{ color: 'var(--text-muted)' }">Track your sales, orders and revenue performance</p>
            </div>
          </div>
          <select v-model="chartPeriod" class="form-input w-auto text-xs py-1.5 px-3" :style="{ background: 'var(--surface-muted)' }">
            <option value="7">Last 7 Days</option>
            <option value="30">Last 30 Days</option>
            <option value="90">Last 90 Days</option>
            <option value="365">12 Months</option>
          </select>
        </div>
        <div class="relative" style="height: 260px;">
          <Line :data="salesChartData" :options="salesChartOptions" />
        </div>
      </div>

      <!-- Order Status Donut -->
      <div class="card p-6 flex flex-col">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-9 h-9 rounded-xl flex items-center justify-center" :style="{ background: 'var(--brand-soft)', color: 'var(--brand)' }">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <div>
            <h3 class="text-sm font-semibold" :style="{ color: 'var(--text)' }">Order Status</h3>
            <p class="text-xs" :style="{ color: 'var(--text-muted)' }">Distribution of orders</p>
          </div>
        </div>
        <div class="flex-1 flex items-center justify-center relative" style="min-height: 180px;">
          <Doughnut :data="donutData" :options="donutOptions" />
          <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="text-center">
              <p class="text-2xl font-bold" :style="{ color: 'var(--text)' }">{{ stats.total_orders ?? 0 }}</p>
              <p class="text-[10px] font-medium" :style="{ color: 'var(--text-muted)' }">Total Orders</p>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-4">
          <div v-for="item in orderStatusLegend" :key="item.label" class="flex items-center gap-2 text-xs">
            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :style="{ background: item.color }"></span>
            <span :style="{ color: 'var(--text-secondary)' }">{{ item.label }}</span>
            <span class="ml-auto font-semibold" :style="{ color: 'var(--text)' }">{{ item.value }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Tables + Quick Actions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="lg:col-span-2 space-y-6">
        <!-- Recent Orders -->
        <div v-if="showRecentOrders" class="card overflow-hidden">
          <div class="flex items-center justify-between px-6 pt-5 pb-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center" :style="{ background: 'var(--brand-soft)', color: 'var(--brand)' }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
              </div>
              <h3 class="text-sm font-semibold" :style="{ color: 'var(--text)' }">Recent Orders</h3>
            </div>
            <Link :href="route('orders.index')" class="text-xs font-semibold" :style="{ color: 'var(--brand)' }">View All →</Link>
          </div>
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th class="pl-6">#</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th class="text-right pr-6">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in stats.recent_orders" :key="order.id">
                  <td class="pl-6 font-medium font-mono text-xs" :style="{ color: 'var(--brand)' }">#{{ order.order_number }}</td>
                  <td :style="{ color: 'var(--text-secondary)' }">{{ order.customer?.name }}</td>
                  <td><StatusBadge :value="order.status" /></td>
                  <td class="text-right pr-6 font-semibold" :style="{ color: 'var(--text)' }">{{ formatCurrency(order.total_amount) }}</td>
                </tr>
                <tr v-if="!stats.recent_orders?.length">
                  <td colspan="4" class="text-center py-8" :style="{ color: 'var(--text-muted)' }">No recent orders</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Payments -->
        <div v-if="showRecentPayments" class="card overflow-hidden">
          <div class="flex items-center justify-between px-6 pt-5 pb-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl flex items-center justify-center" :style="{ background: 'var(--brand-soft)', color: 'var(--brand)' }">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
              </div>
              <h3 class="text-sm font-semibold" :style="{ color: 'var(--text)' }">Recent Payments</h3>
            </div>
            <Link :href="route('payments.index')" class="text-xs font-semibold" :style="{ color: 'var(--brand)' }">View All →</Link>
          </div>
          <div class="overflow-x-auto">
            <table class="data-table">
              <thead>
                <tr>
                  <th class="pl-6">Reference</th>
                  <th>Customer</th>
                  <th>Status</th>
                  <th class="text-right pr-6">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="payment in stats.recent_payments" :key="payment.id">
                  <td class="pl-6 font-mono text-xs font-medium" :style="{ color: 'var(--brand)' }">{{ payment.reference }}</td>
                  <td :style="{ color: 'var(--text-secondary)' }">{{ payment.customer?.name }}</td>
                  <td><StatusBadge :value="payment.status" /></td>
                  <td class="text-right pr-6 font-semibold" :style="{ color: 'var(--text)' }">{{ formatCurrency(payment.amount) }}</td>
                </tr>
                <tr v-if="!stats.recent_payments?.length">
                  <td colspan="4" class="text-center py-8" :style="{ color: 'var(--text-muted)' }">No recent payments</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card p-6">
        <h3 class="text-sm font-semibold mb-4" :style="{ color: 'var(--text)' }">Quick Actions</h3>
        <div class="space-y-2">
          <Link v-for="action in quickActions" :key="action.key" :href="route(action.route)"
            class="flex items-center gap-3 p-3 rounded-xl transition-all group"
            :style="{ border: '1px solid var(--border)' }"
            @mouseenter="$event.currentTarget.style.background = 'var(--brand-soft)'"
            @mouseleave="$event.currentTarget.style.background = 'transparent'">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110"
              :style="{ background: actionIconBg(action.key), color: actionIconColor(action.key) }">
              <span v-html="actionIcon(action.key)" class="w-5 h-5"></span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[13px] font-semibold" :style="{ color: 'var(--text)' }">{{ action.label }}</p>
              <p class="text-[11px]" :style="{ color: 'var(--text-muted)' }">{{ action.description }}</p>
            </div>
            <svg class="w-4 h-4 flex-shrink-0 opacity-40 group-hover:opacity-100 transition-opacity" :style="{ color: 'var(--text-muted)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </Link>
        </div>
      </div>
    </div>

    <!-- Bottom Row: Top Products + Low Stock + Brand Promo -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Top Products -->
      <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" :style="{ background: 'var(--brand-soft)', color: 'var(--brand)' }">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
              <h3 class="text-sm font-semibold" :style="{ color: 'var(--text)' }">Top Products</h3>
              <p class="text-[11px]" :style="{ color: 'var(--text-muted)' }">Your best performing products</p>
            </div>
          </div>
          <Link :href="route('products.index')" class="text-xs font-semibold" :style="{ color: 'var(--brand)' }">View All →</Link>
        </div>
        <div v-if="topProducts.length" class="space-y-3">
          <div v-for="(p, i) in topProducts" :key="p.id" class="flex items-center gap-3">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0"
              :style="{ background: i < 3 ? 'var(--brand-soft)' : 'var(--surface-muted)', color: i < 3 ? 'var(--brand)' : 'var(--text-muted)' }">
              {{ i + 1 }}
            </span>
            <div class="flex-1 min-w-0">
              <p class="text-[13px] font-semibold truncate" :style="{ color: 'var(--text)' }">{{ p.name }}</p>
              <p class="text-[11px]" :style="{ color: 'var(--text-muted)' }">{{ p.sku }}</p>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <p class="text-sm" :style="{ color: 'var(--text-muted)' }">No product data yet</p>
        </div>
      </div>

      <!-- Low Stock Alerts -->
      <div class="card p-6 flex flex-col items-center justify-center text-center">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3" :style="{ background: (stats.low_stock_count ?? 0) > 0 ? 'var(--warning-soft)' : 'var(--success-soft)', color: (stats.low_stock_count ?? 0) > 0 ? 'var(--warning)' : 'var(--success)' }">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <h3 class="text-sm font-semibold mb-1" :style="{ color: 'var(--text)' }">Low Stock Alerts</h3>
        <p class="text-xs mb-3" :style="{ color: 'var(--text-muted)' }">Products that need reordering</p>
        <div v-if="(stats.low_stock_count ?? 0) > 0">
          <p class="text-3xl font-bold mb-1" :style="{ color: 'var(--warning)' }">{{ stats.low_stock_count }}</p>
          <p class="text-xs" :style="{ color: 'var(--text-muted)' }">items below reorder level</p>
        </div>
        <div v-else>
          <svg class="w-10 h-10 mx-auto mb-2 opacity-30" :style="{ color: 'var(--success)' }" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-sm font-medium" :style="{ color: 'var(--success)' }">Great news!</p>
          <p class="text-xs" :style="{ color: 'var(--text-muted)' }">No low stock items right now.</p>
        </div>
      </div>

      <!-- Brand Promo Card -->
      <div class="relative overflow-hidden rounded-2xl flex flex-col justify-end p-6 min-h-[200px]"
        :style="{ background: 'linear-gradient(135deg, #9f5124, #7a3a18)', boxShadow: 'var(--shadow-lg)' }">
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-20 blur-2xl bg-white"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-2 mb-3">
            <img v-if="theme === 'dark'" src="/images/logo_light.png" class="h-6" />
            <img v-else src="/images/logo_light.png" class="h-6" />
            <span class="text-white/80 text-xs font-bold tracking-wide">SUPPLY 4 ME</span>
          </div>
          <h3 class="text-white text-lg font-bold mb-1 leading-tight">Reliable Supply.<br/>Real Growth.</h3>
          <p class="text-white/70 text-xs mb-4">Quality products. Trusted distribution. A stronger tomorrow.</p>
          <Link :href="route('products.index')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all bg-white text-[#9f5124] hover:bg-white/90">
            Explore Products
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
          </Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Line, Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Filler, Tooltip, Legend } from 'chart.js'
import AppLayout from '@/Components/Layout/AppLayout.vue'
import StatusBadge from '@/Components/UI/StatusBadge.vue'
import { Link } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Filler, Tooltip, Legend)

const props = defineProps({
  user: Object,
  stats: Object,
})

const { theme } = useTheme()
const chartPeriod = ref('30')

const currentDate = computed(() => {
    const d = new Date()
    return d.toLocaleDateString('en-GB', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' })
})

const quickActions = computed(() => props.stats.quick_actions ?? [])

const formatNumber = (v) => {
    if (v === undefined || v === null) return '0'
    if (typeof v === 'number' && v >= 1000) return v.toLocaleString()
    return v
}

const formatCurrency = (v) => {
    return new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN', minimumFractionDigits: 2 }).format(v ?? 0)
}

// KPI cards
const kpis = computed(() => [
    {
        label: 'Total Orders',
        value: props.stats.total_orders ?? 0,
        subtitle: `${props.stats.pending_orders ?? 0} pending`,
        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
        iconBg: 'var(--brand-soft)',
        iconColor: 'var(--brand)',
        trend: null,
    },
    {
        label: 'Total Customers',
        value: props.stats.total_customers ?? 0,
        subtitle: 'Active customers',
        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>',
        iconBg: 'var(--info-soft)',
        iconColor: 'var(--info)',
        trend: null,
    },
    {
        label: 'Total Products',
        value: props.stats.total_products ?? 0,
        subtitle: `${props.stats.low_stock_count ?? 0} low stock`,
        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
        iconBg: 'var(--success-soft)',
        iconColor: 'var(--success)',
        trend: null,
    },
    {
        label: 'Pending Payments',
        value: props.stats.pending_payments ?? 0,
        subtitle: 'Awaiting approval',
        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>',
        iconBg: 'var(--warning-soft)',
        iconColor: 'var(--warning)',
        trend: null,
    },
    {
        label: 'Monthly Revenue',
        value: props.stats.monthly_revenue ?? 0,
        prefix: '₦',
        subtitle: 'This month',
        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>',
        iconBg: 'var(--brand-soft)',
        iconColor: 'var(--brand)',
        trend: null,
    },
])

// Chart data — derive from real orders
const salesChartData = computed(() => {
    const orders = props.stats.recent_orders ?? []
    const labels = []
    const revenueData = []
    const orderData = []
    for (let i = parseInt(chartPeriod.value); i >= 1; i--) {
        const d = new Date()
        d.setDate(d.getDate() - i)
        const key = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })
        labels.push(key)
        // Count orders per date
        const dayOrders = orders.filter(o => new Date(o.created_at).toDateString() === d.toDateString())
        orderData.push(dayOrders.length)
        revenueData.push(dayOrders.reduce((s, o) => s + (o.total_amount || 0), 0))
    }
    const isDark = theme.value === 'dark'
    return {
        labels,
        datasets: [
            {
                label: 'Revenue',
                data: revenueData,
                borderColor: '#9f5124',
                backgroundColor: isDark ? 'rgba(197,101,44,0.12)' : 'rgba(159,81,36,0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 5,
                borderWidth: 2,
            },
            {
                label: 'Orders',
                data: orderData,
                borderColor: isDark ? '#60a5fa' : '#2563eb',
                backgroundColor: 'transparent',
                fill: false,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 5,
                borderWidth: 2,
                borderDash: [4, 4],
            }
        ]
    }
})

const salesChartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    interaction: { intersect: false, mode: 'index' },
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: theme.value === 'dark' ? '#1a2742' : '#fff',
            titleColor: theme.value === 'dark' ? '#f0f2f5' : '#202124',
            bodyColor: theme.value === 'dark' ? '#a9b4c2' : '#667085',
            borderColor: theme.value === 'dark' ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.08)',
            borderWidth: 1,
            cornerRadius: 10,
            padding: 12,
        }
    },
    scales: {
        x: { grid: { display: false }, ticks: { color: theme.value === 'dark' ? '#6b7a8d' : '#98a2b3', font: { size: 10 }, maxTicksLimit: 8 }, border: { display: false } },
        y: { grid: { color: theme.value === 'dark' ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.04)' }, ticks: { color: theme.value === 'dark' ? '#6b7a8d' : '#98a2b3', font: { size: 10 } }, border: { display: false } },
    }
}))

// Donut
const orderStatusLegend = computed(() => [
    { label: 'Pending/Draft', value: props.stats.status_pending ?? 0, color: '#98a2b3' },
    { label: 'Processing', value: props.stats.status_processing ?? 0, color: '#d97706' },
    { label: 'Completed', value: props.stats.status_completed ?? 0, color: '#059669' },
    { label: 'Cancelled', value: props.stats.status_cancelled ?? 0, color: '#dc2626' },
])

const donutData = computed(() => ({
    labels: ['Pending', 'Processing', 'Completed', 'Cancelled'],
    datasets: [{
        data: orderStatusLegend.value.map(i => i.value || 0),
        backgroundColor: ['#98a2b3', '#d97706', '#059669', '#dc2626'],
        borderWidth: 0,
        cutout: '70%',
    }]
}))

const donutOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { enabled: true } },
}))

// Top products (placeholder from products count)
const topProducts = computed(() => {
    // Use recent orders customer data as placeholder until top products API exists
    return []
})

// Quick action icon helpers
const ACTION_COLORS = {
    order: { bg: 'rgba(37,99,235,0.10)', color: '#2563eb' },
    customer: { bg: 'rgba(5,150,105,0.10)', color: '#059669' },
    product: { bg: 'rgba(124,58,237,0.10)', color: '#7c3aed' },
    invoice: { bg: 'rgba(217,119,6,0.10)', color: '#d97706' },
    payment: { bg: 'rgba(13,148,136,0.10)', color: '#0d9488' },
    grn: { bg: 'rgba(159,81,36,0.10)', color: '#9f5124' },
    picklist: { bg: 'rgba(99,102,241,0.10)', color: '#6366f1' },
    packinglist: { bg: 'rgba(6,182,212,0.10)', color: '#06b6d4' },
    shipment: { bg: 'rgba(236,72,153,0.10)', color: '#ec4899' },
    delivery: { bg: 'rgba(107,114,128,0.10)', color: '#6b7280' },
}

const actionIconBg = (key) => ACTION_COLORS[key]?.bg || 'var(--brand-soft)'
const actionIconColor = (key) => ACTION_COLORS[key]?.color || 'var(--brand)'

const ACTION_ICONS = {
    order: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
    customer: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>',
    product: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
    invoice: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>',
    payment: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>',
    grn: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>',
    picklist: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
    packinglist: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>',
    shipment: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5a1 1 0 01-1 1h-1M1 16h4m-4-5h14"/></svg>',
    delivery: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>',
}

const actionIcon = (key) => ACTION_ICONS[key] || ACTION_ICONS.order

const showRecentOrders = computed(() => props.stats.can_orders && props.stats.recent_orders?.length > 0)
const showRecentPayments = computed(() => props.stats.can_payments && props.stats.recent_payments?.length > 0)
</script>