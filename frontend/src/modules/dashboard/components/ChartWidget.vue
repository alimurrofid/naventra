<template>
  <div class="bg-white rounded-xl p-4 border border-surface-200 shadow-sm flex flex-col h-full">
    <div class="font-semibold text-lg mb-4 text-surface-900">{{ title }}</div>
    <div class="flex-1 relative min-h-[300px]">
      <Chart type="line" :data="data" :options="chartOptions" class="w-full h-full" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import Chart from 'primevue/chart';
import type { ChartData } from '../types/dashboard.type';

interface Props {
  title: string;
  data: ChartData;
}

defineProps<Props>();

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        usePointStyle: true,
        color: 'var(--p-text-color)'
      }
    }
  },
  scales: {
    x: {
      ticks: {
        color: 'var(--p-text-color-secondary)'
      },
      grid: {
        color: 'var(--p-surface-200)',
        drawBorder: false
      }
    },
    y: {
      ticks: {
        color: 'var(--p-text-color-secondary)'
      },
      grid: {
        color: 'var(--p-surface-200)',
        drawBorder: false
      }
    }
  }
});
</script>

<style scoped>
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-1 { flex: 1 1 0%; }
.h-full { height: 100%; }
.w-full { width: 100%; }
.relative { position: relative; }
.min-h-\[300px\] { min-height: 300px; }
.bg-white { background-color: var(--erp-card); }
.border { border-width: 1px; border-style: solid; }
.border-surface-200 { border-color: var(--erp-border); }
.shadow-sm { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); }
.rounded-xl { border-radius: 12px; }
.p-4 { padding: 1.5rem; }
.mb-4 { margin-bottom: 1.5rem; }
.font-semibold { font-weight: 600; }
.text-lg { font-size: 1.125rem; line-height: 1.5rem; }
.text-surface-900 { color: var(--erp-text-main); }
</style>
