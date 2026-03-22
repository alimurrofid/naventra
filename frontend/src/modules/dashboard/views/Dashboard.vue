<template>
  <div class="flex flex-col gap-6">
    <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-surface-200">
      <div>
        <h1 class="m-0 text-2xl font-bold text-surface-900">Dashboard</h1>
        <p class="m-0 text-surface-500 mt-1">Welcome back, {{ authStore.user?.name || 'User' }}! Here is your business overview.</p>
      </div>
      <div>
        <Button icon="pi pi-refresh" outlined rounded @click="refreshDashboard" :loading="dashboardStore.loading" aria-label="Refresh" />
      </div>
    </div>

    <Message v-if="dashboardStore.error" severity="error" :closable="false">{{ dashboardStore.error }}</Message>

    <!-- Loading Skeleton -->
    <div v-if="dashboardStore.loading && !dashboardStore.data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in 4" :key="i" class="bg-white rounded-xl p-4 h-32 w-full animate-pulse border border-surface-200"></div>
    </div>

    <!-- Data Render -->
    <template v-else-if="dashboardStore.data">
      <!-- KPI Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard 
          title="Total Revenue" 
          :value="dashboardStore.data.stats.revenue" 
          icon="pi-chart-line" 
          type="currency" 
        />
        <StatsCard 
          title="Total Transactions" 
          :value="dashboardStore.data.stats.transactions" 
          icon="pi-file" 
          type="number" 
        />
        <StatsCard 
          title="Active Inventory" 
          :value="dashboardStore.data.stats.inventory" 
          icon="pi-box" 
          type="number" 
        />
        <StatsCard 
          title="Outstanding Receivables" 
          :value="dashboardStore.data.stats.receivables" 
          icon="pi-wallet" 
          type="currency" 
        />
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Chart Section (spanning 7 cols) -->
        <div class="col-span-1 lg:col-span-7">
          <ChartWidget title="Revenue Overview" :data="dashboardStore.data.charts.revenue" />
        </div>
        
        <!-- Recent Transactions Section (spanning 5 cols) -->
        <div class="col-span-1 lg:col-span-5">
          <RecentTransaction :transactions="dashboardStore.data.recent_transactions" />
        </div>
      </div>
    </template>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { useAuthStore } from '@/core/store/auth.store';
import { useDashboardStore } from '../store/dashboard.store';
import Button from 'primevue/button';
import Message from 'primevue/message';
import StatsCard from '../components/StatsCard.vue';
import ChartWidget from '../components/ChartWidget.vue';
import RecentTransaction from '../components/RecentTransaction.vue';

const authStore = useAuthStore();
const dashboardStore = useDashboardStore();

const refreshDashboard = () => {
  dashboardStore.fetchDashboard();
};

onMounted(() => {
  if (!dashboardStore.data) {
    dashboardStore.fetchDashboard();
  }
});
</script>

<style scoped>
.flex { display: flex; }
.flex-col { flex-direction: column; }
.justify-between { justify-content: space-between; }
.items-center { align-items: center; }
.gap-6 { gap: 1.5rem; }
.p-4 { padding: 1rem; }
.m-0 { margin: 0; }
.mt-1 { margin-top: 0.25rem; }
.h-32 { height: 8rem; }
.w-full { width: 100%; }
.bg-white { background-color: var(--erp-card); }
.border { border-width: 1px; border-style: solid; }
.border-surface-200 { border-color: var(--erp-border); }
.shadow-sm { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); }
.rounded-xl { border-radius: 12px; }
.text-2xl { font-size: 1.5rem; line-height: 2rem; }
.font-bold { font-weight: 700; }
.text-surface-900 { color: var(--erp-text-main); }
.text-surface-500 { color: var(--erp-text-muted); }
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
.col-span-1 { grid-column: span 1 / span 1; }

@media (min-width: 768px) {
  .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (min-width: 1024px) {
  .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
  .lg\:grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)); }
  .lg\:col-span-7 { grid-column: span 7 / span 7; }
  .lg\:col-span-5 { grid-column: span 5 / span 5; }
}

/* Base tailwind-like pulse animation */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}
.animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
</style>
