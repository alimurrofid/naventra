<template>
  <div class="flex flex-col gap-4 h-full">
    <BaseCard title="Kas/Bank Keluar (TKbk)">
      <template #content>
        <BaseTable
          :data="tkbkStore.items"
          :columns="columns"
          :loading="tkbkStore.loading"
          :totalRecords="tkbkStore.totalRecords"
          :paginator="true"
          :lazy="true"
          @page="onPage"
          @filter="onFilter"
          dataKey="id"
          selectionMode="multiple"
          v-model:selection="selectedRows"
          :actions="workflowActions"
          statusField="status"
          @action="handleAction"
        >
          <template #toolbar-right>
            <BaseButton 
              permission="accounting.t_kbk.create" 
              label="Create New" 
              icon="pi pi-plus" 
              @click="$router.push('/accounting/t_kbk/create')" 
            />
          </template>

          <template #body-total_amount="{ data }">
            {{ formatCurrency(data.total_amount) }}
          </template>

          <template #body-status="{ data }">
            <Badge :value="data.status" :severity="getSeverity(data.status)" />
          </template>

          <template #row-actions="{ data }">
            <div class="flex gap-2 justify-center">
              <BaseButton 
                permission="accounting.t_kbk.read" 
                icon="pi pi-eye" 
                text 
                rounded 
                severity="secondary" 
                @click="$router.push(`/accounting/t_kbk/${data.id}`)" 
              />
              <BaseButton 
                v-if="data.status === 'DRAFT'"
                permission="accounting.t_kbk.delete" 
                icon="pi pi-trash" 
                text 
                rounded 
                severity="danger" 
                @click="confirmDelete(data)" 
              />
            </div>
          </template>
        </BaseTable>
      </template>
    </BaseCard>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useTKbkStore } from '../store/tkbk.store';
import BaseCard from '@/core/components/BaseCard.vue';
import BaseTable from '@/core/components/BaseTable.vue';
import BaseButton from '@/core/components/BaseButton.vue';
import Badge from 'primevue/badge';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import type { WorkflowAction } from '@/core/composables/useWorkflow';

const tkbkStore = useTKbkStore();
const confirm = useConfirm();
const toast = useToast();
const selectedRows = ref([]);

const columns = [
  { field: 'transaction_number', header: 'Trans No' },
  { field: 'transaction_date', header: 'Date' },
  { field: 'transaction_type', header: 'Type' },
  { field: 'description', header: 'Description' },
  { field: 'total_amount', header: 'Total Amount' },
  { field: 'status', header: 'Status' }
];

// Define workflow actions config based on backend rules
const workflowActions: WorkflowAction[] = [
  {
    key: 'post',
    label: 'Post',
    icon: 'pi pi-send',
    permission: 'accounting.t_kbk.post',
    statuses: ['DRAFT'],
    severity: 'info',
    isBulk: true
  },
  {
    key: 'request_approval',
    label: 'Request Approval',
    icon: 'pi pi-bell',
    permission: 'accounting.t_kbk.request_approval',
    statuses: ['POST'],
    severity: 'warn',
    isBulk: true
  },
  {
    key: 'approve',
    label: 'Approve',
    icon: 'pi pi-check-circle',
    permission: 'accounting.t_kbk.approve',
    statuses: ['APPROVAL'],
    severity: 'success',
    isBulk: true
  },
  {
    key: 'reject',
    label: 'Reject',
    icon: 'pi pi-times-circle',
    permission: 'accounting.t_kbk.reject',
    statuses: ['APPROVAL'],
    severity: 'danger',
    isBulk: true
  }
];

onMounted(() => {
  loadData();
});

const loadData = (page = 1, filters = {}) => {
  tkbkStore.fetchAll(filters, page);
};

const onPage = (e: any) => {
  loadData(e.page + 1); // PrimeVue pages are 0-indexed, backend usually 1-indexed
};

const onFilter = (filters: any) => {
  const apiFilters = {
    search: filters.global?.value || undefined
  };
  loadData(1, apiFilters);
};

const formatCurrency = (val: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR'
  }).format(val || 0);
};

const getSeverity = (status: string) => {
  switch (status?.toUpperCase()) {
    case 'DRAFT': return 'secondary';
    case 'POST': return 'info';
    case 'APPROVAL': return 'warn';
    case 'APPROVED': return 'success';
    case 'REJECTED': return 'danger';
    default: return 'info';
  }
};

const handleAction = async (payload: { action: string, items: any[] }) => {
  try {
    const ids = payload.items.map(i => i.id);
    let targetStatus = '';
    
    switch (payload.action) {
      case 'post': targetStatus = 'POST'; break;
      case 'request_approval': targetStatus = 'APPROVAL'; break;
      case 'approve': targetStatus = 'APPROVED'; break;
      case 'reject': targetStatus = 'REJECTED'; break;
    }

    if (ids.length === 1) {
      await tkbkStore.changeStatus(ids[0], targetStatus);
    } else {
      await tkbkStore.bulkChangeStatus(ids, targetStatus);
    }
    
    // Refresh table and clear selection
    selectedRows.value = [];
    loadData();
    toast.add({ severity: 'success', summary: 'Success', detail: 'Action completed', life: 3000 });
  } catch (e: any) {
    toast.add({ severity: 'error', summary: 'Error', detail: e.message, life: 5000 });
  }
};

const confirmDelete = (data: any) => {
  confirm.require({
    message: `Delete transaction ${data.transaction_number}?`,
    header: 'Confirmation',
    icon: 'pi pi-exclamation-triangle',
    acceptClass: 'p-button-danger',
    accept: async () => {
      try {
        await tkbkStore.remove(data.id);
        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Successfully deleted', life: 3000 });
        loadData();
      } catch (e: any) {
        toast.add({ severity: 'error', summary: 'Error', detail: e.message, life: 5000 });
      }
    }
  });
};
</script>

<style scoped>
.flex { display: flex; }
.flex-col { flex-direction: column; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.h-full { height: 100%; }
.justify-center { justify-content: center; }
</style>
