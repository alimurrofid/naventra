<template>
  <div>
    <BaseCard title="Chart of Accounts">
      <template #content>
        <!-- Toolbar custom slots -->
        <BaseTable
          :data="mcoaStore.treeItems"
          :columns="columns"
          :loading="mcoaStore.loading"
          :paginator="false"
          :lazy="false"
          dataKey="id"
          selectionMode="single"
          v-model:selection="selectedRow"
        >
          <template #toolbar-right>
            <BaseButton 
              permission="accounting.m_coa.create" 
              label="Create New" 
              icon="pi pi-plus" 
              @click="openForm()" 
            />
          </template>

          <!-- Custom column rendering for Code (indentation for tree effect) -->
          <template #body-code="{ data }">
            <div :style="{ paddingLeft: `${getDepth(data) * 1.5}rem` }" class="flex items-center gap-2">
              <i v-if="data.type === 'HEADER'" class="pi pi-folder text-primary-500"></i>
              <i v-else class="pi pi-file text-surface-500"></i>
              <span :class="{'font-bold': data.type === 'HEADER'}">{{ data.code }}</span>
            </div>
          </template>

          <!-- Status Column -->
          <template #body-is_active="{ data }">
            <Badge :value="data.is_active ? 'Active' : 'Inactive'" :severity="data.is_active ? 'success' : 'danger'" />
          </template>

          <!-- Actions -->
          <template #row-actions="{ data }">
            <div class="flex gap-2 justify-center">
              <BaseButton 
                permission="accounting.m_coa.update" 
                icon="pi pi-pencil" 
                text 
                rounded 
                severity="info" 
                @click="openForm(data.id)" 
              />
              <BaseButton 
                permission="accounting.m_coa.delete" 
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

    <!-- MCoa Form Dialog (Could also be a separate route, using Dialog for simplicity) -->
    <Dialog v-model:visible="formDialog" :header="formTitle" :modal="true" :style="{ width: '450px' }" class="p-fluid">
      <MCoaForm v-if="formDialog" :id="editingId" @saved="onSaved" @cancel="formDialog = false" />
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useMCoaStore } from '../store/mcoa.store';
import BaseCard from '@/core/components/BaseCard.vue';
import BaseTable from '@/core/components/BaseTable.vue';
import BaseButton from '@/core/components/BaseButton.vue';
import MCoaForm from './MCoaForm.vue';
import Badge from 'primevue/badge';
import Dialog from 'primevue/dialog';

const mcoaStore = useMCoaStore();
// const confirm = useConfirm();
// const toast = useToast();

const selectedRow = ref();
const formDialog = ref(false);
const editingId = ref<number | null>(null);
const formTitle = ref('');

const columns = [
  { field: 'code', header: 'Account Code', sortable: false },
  { field: 'name', header: 'Account Name', sortable: false },
  { field: 'type', header: 'Type', sortable: false },
  { field: 'balance_type', header: 'Normal Balance', sortable: false },
  { field: 'is_active', header: 'Status', sortable: false },
];

onMounted(() => {
  loadData();
});

const loadData = () => {
  // Using tree for better visualization of hierarchies
  mcoaStore.fetchTree();
};

const getDepth = (node: any, depth = 0): number => {
  if (!node.parent_id) return depth;
  // This is a simplified frontend depth calculation. In a proper tree table, node depth is known.
  // Assuming the API returns a flattened tree ordered by hierarchy.
  return depth; 
};

const openForm = (id?: number) => {
  editingId.value = id || null;
  formTitle.value = id ? 'Edit Account' : 'Create Account';
  formDialog.value = true;
};

const onSaved = () => {
  formDialog.value = false;
  loadData();
};

const confirmDelete = async (data: any) => {
  if (confirm(`Are you sure you want to delete ${data.name}?`)) {
    try {
      await mcoaStore.remove(data.id);
      loadData();
    } catch (e: any) {
      alert(e.message);
    }
  }
};
</script>

<style scoped>
.flex { display: flex; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.gap-2 { gap: 0.5rem; }
.font-bold { font-weight: 700; }
.text-primary-500 { color: var(--p-primary-500); }
.text-surface-500 { color: var(--p-surface-500); }
</style>
