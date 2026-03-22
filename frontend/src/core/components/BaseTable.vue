<template>
  <div class="flex flex-col h-full gap-4 min-h-0 flex-1">
    <!-- Toolbar -->
    <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-surface-200 mb-2 flex-wrap gap-3">
      <div class="flex items-center gap-3">
        <IconField iconPosition="left">
          <InputIcon class="pi pi-search"> </InputIcon>
          <InputText 
            v-model="filters['global'].value" 
            placeholder="Keyword Search" 
            @input="onFilter"
          />
        </IconField>

        <!-- Inline Action Bar shown when rows are selected -->
        <transition name="fade">
          <BaseActionBar 
            v-if="selectedRows.length > 0 && actions.length > 0"
            :selectedRows="selectedRows"
            :actions="actions"
            :statusField="statusField"
            @action="$emit('action', $event)"
          />
        </transition>
      </div>
      <div class="flex items-center gap-2">
        <slot name="toolbar-right"></slot>
      </div>
    </div>

    <!-- Data Table -->
    <DataTable
      v-bind="$attrs"
      :value="data"
      :loading="loading"
      :paginator="paginator"
      :rows="rows"
      :totalRecords="totalRecords"
      :lazy="lazy"
      @page="onPage"
      @sort="onSort"
      @filter="onFilterAjax"
      v-model:selection="selectedRows"
      :dataKey="dataKey"
      responsiveLayout="scroll"
      scrollable
      scrollHeight="flex"
      stripedRows
      class="flex-1 min-h-0 bg-white rounded-lg border border-surface-200 overflow-hidden shadow-none p-datatable-sm flex flex-col"
    >
      <!-- Ensure empty state looks good -->
      <template #empty>
        <div class="p-6 text-center text-surface-500">
          No records found.
        </div>
      </template>
      
      <template #loading>
        <div class="p-6 text-center text-surface-500">
          Loading records. Please wait...
        </div>
      </template>

      <!-- Selection Column -->
      <Column v-if="selectionMode" :selectionMode="selectionMode" headerStyle="width: 3rem"></Column>

      <!-- Dynamic Columns -->
      <Column 
        v-for="col in columns" 
        :key="col.field" 
        :field="col.field" 
        :header="col.header" 
        :sortable="col.sortable !== false"
        :style="col.style"
      >
        <template #body="slotProps">
          <slot :name="`body-${col.field}`" :data="slotProps.data" :field="col.field">
            {{ slotProps.data[col.field] }}
          </slot>
        </template>
      </Column>

      <!-- Row Actions Column (always at end) -->
      <Column v-if="$slots['row-actions']" bodyStyle="text-align:center" style="min-width: 8rem" frozen alignFrozen="right">
        <template #body="slotProps">
          <slot name="row-actions" :data="slotProps.data"></slot>
        </template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import DataTable from 'primevue/datatable';
import type { DataTablePageEvent, DataTableSortEvent, DataTableFilterEvent } from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import BaseActionBar from './BaseActionBar.vue';
import type { WorkflowAction } from '@/core/composables/useWorkflow';

interface ColumnDef {
  field: string;
  header: string;
  sortable?: boolean;
  style?: string;
}

interface Props {
  data: any[];
  columns: ColumnDef[];
  totalRecords?: number;
  loading?: boolean;
  lazy?: boolean;
  paginator?: boolean;
  rows?: number;
  dataKey?: string;
  selectionMode?: 'single' | 'multiple' | null;
  actions?: WorkflowAction[];
  statusField?: string;
  scrollHeight?: string;
}

const props = withDefaults(defineProps<Props>(), {
  totalRecords: 0,
  loading: false,
  lazy: true,
  paginator: true,
  rows: 10,
  dataKey: 'id',
  selectionMode: 'multiple',
  actions: () => [],
  statusField: 'status',
  scrollHeight: 'flex'
});

const emit = defineEmits(['page', 'sort', 'filter', 'action', 'update:selection']);

const selectedRows = ref<any[]>([]);

// Sync internal selection with parent if needed
watch(selectedRows, (newVal) => {
  emit('update:selection', newVal);
});

// Basic filter state
const filters = ref({
  global: { value: null, matchMode: 'contains' }
});

let filterTimeout: any = null;

const onFilter = () => {
  if (filterTimeout) clearTimeout(filterTimeout);
  filterTimeout = setTimeout(() => {
    emit('filter', filters.value);
  }, 500); // debounce API call
};

const onPage = (e: DataTablePageEvent) => {
  emit('page', e);
};

const onSort = (e: DataTableSortEvent) => {
  emit('sort', e);
};

const onFilterAjax = (e: DataTableFilterEvent) => {
  emit('filter', e.filters);
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.h-full { height: 100%; }
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-1 { flex: 1 1 0%; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.p-3 { padding: 0.75rem; }
.p-6 { padding: 1.5rem; }
.text-center { text-align: center; }
.rounded-lg { border-radius: 0.5rem; }
.border { border-width: 1px; border-style: solid; }
.overflow-hidden { overflow: hidden; }
.mb-2 { margin-bottom: 0.5rem; }

.bg-white { background-color: var(--erp-card); }
.text-surface-500 { color: var(--erp-text-muted); }
.border-surface-200 { border-color: var(--erp-border); }
</style>
