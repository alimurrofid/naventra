<template>
  <div class="bg-white rounded-xl p-4 border border-surface-200 shadow-sm flex items-center justify-between">
    <div class="flex-1 min-w-0 mr-4">
      <span class="block text-surface-500 font-medium mb-3 truncate">{{ title }}</span>
      <div class="text-surface-900 font-bold text-2xl truncate" :title="formattedValue">
        {{ formattedValue }}
      </div>
    </div>
    <div class="flex items-center justify-center bg-primary-100 rounded-lg p-3 w-12 h-12">
      <i :class="['pi text-xl text-primary-500', icon]"></i>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  title: string;
  value: number | string;
  icon: string;
  type?: 'currency' | 'number';
}

const props = withDefaults(defineProps<Props>(), {
  type: 'number'
});

const formattedValue = computed(() => {
  const numValue = Number(props.value) || 0;
  if (props.type === 'currency') {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      maximumFractionDigits: 0
    }).format(numValue);
  }
  return new Intl.NumberFormat('id-ID').format(numValue);
});
</script>

<style scoped>
.flex { display: flex; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.justify-center { justify-content: center; }
.bg-white { background-color: var(--erp-card); }
.border { border-width: 1px; border-style: solid; }
.border-surface-200 { border-color: var(--erp-border); }
.shadow-sm { box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); }
.rounded-xl { border-radius: 12px; }
.rounded-lg { border-radius: 8px; }
.p-4 { padding: 1.25rem; }
.p-3 { padding: 0.75rem; }
.mb-3 { margin-bottom: 0.5rem; }
.block { display: block; }
.text-surface-500 { color: var(--erp-text-muted); }
.text-surface-900 { color: var(--erp-text-main); }
.font-medium { font-weight: 500; }
.font-bold { font-weight: 700; }
.text-2xl { font-size: 1.5rem; line-height: 2rem; }
.text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.w-12 { width: 3.5rem; }
.h-12 { height: 3.5rem; }
.flex-1 { flex: 1 1 0%; }
.min-w-0 { min-width: 0px; }
.mr-4 { margin-right: 1rem; }
.truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.bg-primary-100 { background-color: #eff6ff; }
.text-primary-500 { color: #3b82f6; }
</style>
