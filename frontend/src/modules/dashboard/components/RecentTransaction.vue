<template>
  <div class="bg-white rounded-xl p-4 border border-surface-200 shadow-sm flex flex-col h-full">
    <div class="flex justify-between items-center mb-4">
      <div class="font-semibold text-lg text-surface-900">Recent Transactions</div>
      <Button label="View All" text size="small" @click="$router.push('/accounting/t_kbk')" />
    </div>
    
    <DataTable :value="transactions" :rows="5" responsiveLayout="scroll" class="p-datatable-sm w-full">
      <Column field="transaction_date" header="Date"></Column>
      <Column field="transaction_number" header="Trans No"></Column>
      <Column field="description" header="Description"></Column>
      <Column field="total_amount" header="Amount">
        <template #body="slotProps">
          {{ formatCurrency(slotProps.data.total_amount) }}
        </template>
      </Column>
      <Column field="status" header="Status">
        <template #body="slotProps">
          <Badge :value="slotProps.data.status" :severity="getSeverity(slotProps.data.status)" />
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Badge from 'primevue/badge';
import type { RecentTransaction } from '../types/dashboard.type';

interface Props {
  transactions: RecentTransaction[];
}

defineProps<Props>();

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val);
};

const getSeverity = (status: string) => {
  switch (status.toUpperCase()) {
    case 'DRAFT': return 'secondary';
    case 'POST': return 'info';
    case 'APPROVAL': return 'warn';
    case 'APPROVED': return 'success';
    case 'REJECTED': return 'danger';
    default: return 'info';
  }
};
</script>

<style scoped>
.flex { display: flex; }
.flex-col { flex-direction: column; }
.justify-between { justify-content: space-between; }
.items-center { align-items: center; }
.h-full { height: 100%; }
.w-full { width: 100%; }
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
